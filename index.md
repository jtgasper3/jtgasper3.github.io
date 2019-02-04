---
layout: default
---

# Latest Posts
{% for post in site.posts limit:4 %}
## [{{ post.title }}]({{ post.url }})

{{ post.excerpt }}

[Read more]({{ post.url }})

---
{% endfor %}

[More posts]({{ site.blog_path }})