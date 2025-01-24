---
layout: default
---

<iframe src="https://ghbtns.com/github-btn.html?user=jtgasper3&type=follow&count=true&size=large" frameborder="0" scrolling="0" width="230" height="30" title="GitHub"></iframe>

<a style="display:inline-block;background-color:#FC5200;color:#fff;padding:5px 10px 5px 30px;font-size:11px;font-family:Helvetica, Arial, sans-serif;white-space:nowrap;text-decoration:none;background-repeat:no-repeat;background-position:10px center;border-radius:3px;background-image:url('//badges.strava.com/logo-strava-echelon.png')" href='https://www.strava.com/athletes/jtgasper3' target="_clean">
  Follow me on
  <img src='//badges.strava.com/logo-strava.png' alt='Strava' style='margin-left:2px;vertical-align:text-bottom' height=13 width=51 />
</a>

# Latest Posts

{% for post in site.posts limit:4 %}

## [{{ post.title }}]({{ post.url }})

{{ post.excerpt }}

[Read more]({{ post.url }})

---

{% endfor %}

[More posts]({{ site.blog_path }})
