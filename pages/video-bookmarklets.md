---
layout: page
title: Video Player Bookmarklets
---

Some web page video players, like on iPhones and iPads, have buttons on the video playback controls to back up or advance the video by 10 or 15 seconds at a time. Most desktop browser-based video players do not support this, but wouldn't it be nice if they did?

This page has links, which are called "bookmarklets" (because they don't link to web pages but execute commands within the web page) that will allow you to back up or advance the video being played in an HTML5 video player (e.g. Canvas, YouTube). By dragging them into your browser's Favorite/Bookmark toolbar, you can use them on any web page.

To install them:

1. If not already enabled, turn on the browser's Favorites/Bookmark toolbar. ([How do I enable the toolbar?](https://www.computerhope.com/issues/ch001917.htm)).
1. Drag one or more links onto the Bookmark Bar:
    - [Video: -15s](javascript:document.querySelector("video").currentTime-=15)
    - [Video: -10s](javascript:document.querySelector("video").currentTime-=10)
    - [Video: -5s](javascript:document.querySelector("video").currentTime-=5)
    - [Video: +5s](javascript:document.querySelector("video").currentTime+=5)
    - [Video: +10s](javascript:document.querySelector("video").currentTime+=10)
    - [Video: +15s](javascript:document.querySelector("video").currentTime+=15)
1. Now they are installed.

To use them:
1. Start a video on a web page.
1. Just click the appropriate button/bookmark to move the video back and forth.

Other notes:
1. These can be renamed or deleted by right clicking on them and selecting the appropriate menu item.
2. Not every website uses the HTML5 video player. They buttons/bookmarks will not work for everything. Sorry.


## Experimental

This bookmarklet adds buttons directly to the Canvas video's playback controls. They are ugly but work.

To use it:

1. Drag the link to your bookmark bar: [Add Canvas Video Btn](javascript:document.querySelector("div.mejs-playpause-button").insertAdjacentHTML("beforeBegin","<div><button onclick='document.querySelector(\"video\").currentTime-=5'>-5</button><button onclick='document.querySelector(\"video\").currentTime+=5'>+5</button></div>"))
1. After starting the Canvas video, click the bookmark/button once. This adds `-5` and `+5` buttons on the video playback controls.

Now you can use the `-5` and `+5` buttons on the video playback controls, instead of using the individual bookmarks/buttons in the Bookmark/Favorites bar.
