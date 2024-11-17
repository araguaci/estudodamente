---
layout: page
title: Install
description: >
  How you install DeepDive depends on whether you start a new site,
  or change the theme of an existing site.
hide_description: true
# sitemap: false
---

How you install DeepDive depends on whether you [start a new site](#new-sites),
or change the theme of [an existing site](#existing-sites).

0. this unordered seed list will be replaced by toc as unordered list
{:toc}

## New sites

For new sites, the best way to get started with DeepDive is via the Starter Kit.
It comes with a documented config file and example content that gets you started quickly.

If you have a GitHub account, fork the [DeepDive Starter Kit][hsc] repository.
Otherwise [download the Starter Kit][src] and unzip them somewhere on your machine.

In addition to the docs here, you can follow the quick start guide in the Starter Kit.
{:.note}

You can now jump to [running locally](#running-locally).

You can now also [![Deploy to Netlify][dtn]][nfy]{:.no-mark-external} directly.
{:.note}

[hsc]: https://github.com/hydecorp/hydejack-starter-kit
[src]: https://github.com/hydecorp/hydejack-starter-kit/archive/v9.1.6.zip
[nfy]: https://app.netlify.com/start/deploy?repository=https://github.com/hydecorp/hydejack-starter-kit
[dtn]: https://www.netlify.com/img/deploy/button.svg

### Troubleshooting

If your existing site combines theme files with your content, make sure to delete the following folders:

- `_layouts`
- `_includes`
- `_sass`
- `assets`

The `assets` folder most likely includes theme files as well as your personal/content files.
Make sure to only delete files that belong to the old theme!

## GitHub Pages

If you want to build your site on [GitHub Pages][ghp], check out the [`gh-pages` branch][gpb] in the DeepDive Starter Kit repo.

[ghp]: https://jekyllrb.com/docs/github-pages/
[gpb]: https://github.com/hydecorp/hydejack-starter-kit/tree/gh-pages

For existing sites, you can instead set the `remote_theme` key as follows:

```yml
# file: `_config.yml`
remote_theme: hydecorp/deepdive@v9.1.6
```

Make sure the `plugins` list contains `jekyll-include-cache` (create if it doesn't exist):
{:.note title="Important"}

```yml
# file: `_config.yml`
plugins:
  - jekyll-include-cache
```

To run this configuration locally, make sure the following is part of your `Gemfile`:

```ruby
# file: `Gemfile`
gem "github-pages", group: :jekyll_plugins
gem "jekyll-include-cache", group: :jekyll_plugins
```

Note that DeepDive has a reduced feature set when built on GitHub Pages.
Specifically, using KaTeX math formulas doesn't work when built in this way.
{:.note}

## Running locally

Make sure you've `cd`ed into the directory where `_config.yml` is located.
Before running for the first time, dependencies need to be fetched from [RubyGems](https://rubygems.org/):

~~~bash
bundle install
~~~

If you are missing the `bundle` command, you can install Bundler by running `gem install bundler`.
{:.note}

Now you can run Jekyll on your local machine:

~~~bash
bundle exec jekyll serve
~~~

and point your browser to <http://localhost:4000> to see DeepDive in action.

Continue with [Config](config.md){:.heading.flip-title}
{:.read-more}

