---
layout: page
title: Scripts
description: >
  There are two ways of adding third party scripts.
  Embedding is ideal for one-off scripts, while global scripts are loaded on every page.
hide_description: true
# sitemap: false
---

There are two ways of adding third party scripts.
[Embedding](#embedding) is ideal for one-off scripts, e.g. `widgets.js` that is part of embedded tweets (see below).
Adding [global scripts](#global-scripts) is for scripts that should be loaded on every page.

0. this unordered seed list will be replaced by toc as unordered list
{:toc}

## Embedding

DeepDive supports embedding third party scripts directly inside markdown content. This will work in most cases, except when a script can not be loaded on a page more than once (this will occur when a user navigates to the same page twice).

Example:

~~~html
<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
<blockquote class="twitter-tweet" data-lang="en">
  <p lang="en" dir="ltr">
    📨 I’ve been using @ProtonMail and thought you might like it.
    It’s a secure email service that protects your privacy.
    Sign up with my referral link to get 1 month of premium features for free.
    <a href="https://account.proton.me/refer-a-friend?referrer=XG0V6375XEEG">
      https://account.proton.me/refer-a-friend?referrer=XG0V6375XEEG
    </a>
  </p>
  &mdash; JV conseil (@JVconseil)
  <a href="https://twitter.com/JVconseil/status/1654523745321598976">June 3, 2017</a>
</blockquote>
~~~

<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
<blockquote class="twitter-tweet" data-lang="en"><p lang="en" dir="ltr">📨 I’ve been using @ProtonMail and thought you might like it. It’s a secure email service that protects your privacy. Sign up with my referral link to get 1 month of premium features for free. https://account.proton.me/refer-a-friend?referrer=XG0V6375XEEG</p>&mdash; JV conseil (@JVconseil) <a href="https://twitter.com/JVconseil/status/1654523745321598976">May 5, 2023</a></blockquote>

## Global scripts

If you have scripts that should be included on every page you can add them globally by
opening (or creating) `_includes/my-scripts.html` and adding them like you normally would:

```html
<!-- file: `_includes/my-scripts.html` -->
<script
  src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
  integrity="sha256-k2WSCIexGzOj3Euiig+TlR8gA0EmPjuc79OEeY5L45g="
  crossorigin="anonymous"></script>
```

`my-scripts.html` will be included at the end of the `body` tag.

## Registering push state event listeners

When embedding scripts globally you might want to run some init code after each page load. However, the problem with push state-based page loads is that the `load` event won't fire again. Luckily, DeepDive's push state component exposes an event that you can listen to instead.

```html
<!-- file: `_includes/my-scripts.html` -->
<script>
  document.getElementById('_pushState').addEventListener('hy-push-state-load', function() {
    // <your init code>
  });
</script>
```

Note that the above code must only run once, so include it in your `my-scripts.html`.

`hy-push-state-start`
: Occurs after clicking a link.

`hy-push-state-ready`
: Animation fished and response has been parsed, ready to swap out the content.

`hy-push-state-after`
: The old content has been replaced with the new content.

`hy-push-state-progress`
: Special case when animation is finished, but no response from server has arrived yet.
  This is when the loading spinner will appear.

`hy-push-state-load`
: All embedded script tags have been inserted into the document and have finished loading.

## If everything else fails

If you can't make an external script work with DeepDive's push state approach to page loading,
you can disable push state by adding to your config file:

```yml
# file: `_config.yml`
deepdive:
  no_push_state: true
```

Continue with [Build](build.md){:.heading.flip-title}
{:.read-more}
