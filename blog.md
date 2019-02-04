---
layout: default
title: Blog
---

[Archive]({{ site.archive_path }}) |
[Tags]({{ site.tags_path }}) |
[RSS Feed](/rss.xml)

# Latest Posts

{% for post in site.posts limit:20 %}
  
## [{{ post.title }}]({{ post.url }})

<cite>{{ post.date | date_to_string }}</cite>

{{ post.excerpt }}
  
{% endfor %}

[Full Archives]({{ site.archive_path }})