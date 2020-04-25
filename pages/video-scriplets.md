---
layout: page
title: Video Player Scriptlets
---

Some web page video players, like on iPhones and iPads, have buttons on the video control bar to back up or advance the video by 10 or 15 seconds at a time. Most desktop browser's video players do not support this. This page has links, which are called "scriptlets" (because they don't link to pages but execute JavaScript commands) that will allow you to back up or advance the video being played in an HTML5 video player (e.g. Canvas, YouTube). By dragging them into your browser's Favorite/Bookmark toolbar, you can use them on any web page.

To install them:

1. If not already enabled, turn on the browser's Favorites/Bookmark toolbar. ([How do I enable the toolbar?](https://www.computerhope.com/issues/ch001917.htm)).
1. Drag one or more links onto the Bookmark Bar:
    - [Video: -15s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime-15)
    - [Video: -10s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime-10)
    - [Video: -5s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime-5)
    - [Video: +5s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime+5)
    - [Video: +10s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime+10)
    - [Video: +15s](javascript:document.querySelector("video").currentTime=document.querySelector("video").currentTime+15)
1. Now they are installed.

To use the:
1. Start a 
1. Just click the appropriate button/bookmark to move the video back and forth.

Other notes:
1. These can be renamed or deleted by right clicking on them and selecting the appropriate menu item.
2. Not every website uses the HTML5 video player. They buttons/bookmarks will not work for everything. Sorry.
