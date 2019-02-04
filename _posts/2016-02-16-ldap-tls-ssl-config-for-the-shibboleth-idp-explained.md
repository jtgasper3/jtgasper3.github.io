---
layout: post
tags: [saml, iam, sso, unicon]
---

I posted a new blog entry on my [Unicon](https://www.unicon.net/about/blogs/blogger/177) blog:

> Many Shibboleth IdP adopters use LDAP as provide both an authentication provider and an attribute source. There is always the question of "do we need to configure TLS/SSL for the IdP's connection to the LDAP server(s)?". My response is "always" because we need to protect the user's credentials even in the most trusted network. My question back to the client, "Why wouldn't you?". Often the response is somewhere between "we've tried and we got it to work once, but then it broke sometime" and "we could never get it to work"...

<!--more-->

Read more at <https://www.unicon.net/about/blogs/ldap-tlsssl-config-shibboleth-idp-explained>