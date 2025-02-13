---
title: Getting ExchangeOnlineManagement Access Tokens
layout: post
excerpt: After some investigation I found out how to get the access token from `Connect-ExchangeOnline`.
tags: [exchange, powershell, microsoft]
---

I had a colleague reach out with a question. They have a PowerShell-based app that uses some GUI framework that uses the Windows Forms library to build visual apps.
The framework uses PowerShell "jobs" to handle blocking calls, like querying the Graph or Exchange Online apis, so the UI remains responsive while the background task occurs. 
Because each job has its own PowerShell session, he often passes a credential object into the job so it can make the authenticated API call. Apparently, Microsoft changed a PS module
in a way it no longer worked for him. What he really needed was to get the credential created by `Connect-ExchangeOnline` so he could pass that into the various jobs, 
so the end users of his tool didn't constantly get prompted for authentication.

Googling and using Chat GPT only returned old answers of querying the PSSessions and getting an access token from there, but that does not currently work. After some investigation I found
how to get the access token from `Connect-ExchangeOnline`.

The following demonstrates how to achieve this:

```powershell
# Connect to Exchange Online
Import-Module ExchangeOnlineManagement

$upn = "john.gasper@example.org"

# Initial Authn
Connect-ExchangeOnline -UserPrincipalName $upn -ShowProgress $false

# Get the bundle of connection contexts
$contexts = [Microsoft.Exchange.Management.ExoPowershellSnapin.ConnectionContextFactory]::GetAllConnectionContexts()

# Filter the  contexts list for SessionPrefixName of ExchangeOnlineInternalSession_ and ExchangeEnvironmentName of O365Default
$filteredContexts = $contexts | Where-Object {
    $_.SessionPrefixName -eq 'ExchangeOnlineInternalSession_' -and
    $_.ExchangeEnvironmentName -eq 'O365Default'
}

# Grab the access token from the first context
$token = $filteredContexts[0].PowerShellTokenInfo.AuthorizationHeader.Replace("Bearer ","")

# Function to run in each job
$scriptBlock = {
    param($token, $userpn)
    Import-Module ExchangeOnlineManagement
    Connect-ExchangeOnline -UserPrincipalName $userpn -AccessToken $token
    Get-Mailbox
}

# Start jobs
$jobs = @()
for ($i = 1; $i -le 3; $i++) {
    $jobs += Start-Job -ScriptBlock $scriptBlock -ArgumentList $token, $upn 
}

# Wait for all jobs to complete
$jobs | ForEach-Object { $_ | Wait-Job | Receive-Job }

# Clean up
$jobs | ForEach-Object { Remove-Job -Job $_ }
Disconnect-ExchangeOnline -Confirm:$false
```
