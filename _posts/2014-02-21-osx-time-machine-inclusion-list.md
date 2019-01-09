---
layout: post
title: OS X Time Machine Inclusion List
tags: [osx,time machine]
---
So after being a Microsoft Windows person for forever... when my new company let me choose between a PC and a Mac I decided to go with a Mac Book Pro. Almost everyone on my team was using them, so I figured why not. One of my co-workers told me about Time Machine, so it was one of the first features I enabled.

Time Machine backups everything, and I only wanted it to back up documents, git & svn working copies, etc. It turns out you can only exclude items. (If you are reading this you know what I'm talking about.) After a while of playing I got my backups down to a few gigs, but that didn't last long. I started adding apps and those apps started storing their own data (~/Library/) and it grew quickly. Within in a couple of months my backups where 27gb and my backup drive was only 20gb (well, I allocated 20gb on drive connected to my router). I needed to find this massive store of data, and I couldn't find anything on the web to help me.

Eventually I worked this out...

```
   find ~ -type d -print0 | xargs -0 tmutil isexcluded > ~/dump.txt
```
This iterates over ever directory (in this case starting in my home directory, ~), and runs it through Time Machine's command line utility (tmutil) calling the isexcluded directive. `isexcluded` will report back whether a given file or directory is included or excluded from the back. Finally, the results are dumped into the dump.txt file in the home directory.


It only took me a few minutes of looking through `dump.txt` to realize the culprit of my issue, `~/Library`. Now I'm back down to a base backup set of 4gb.

Hopefully this can help out someone else on the Interweb.

Disclaimer: Excluding hidden directories should be done with caution. There is a reason they are hidden, some of the data maybe important to you. Don't exclude directories unless you know how it will impact you if you ever need to recover.