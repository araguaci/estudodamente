---
layout: page
title: Upgrade
description: >
  This chapter shows how to upgrade DeepDive to a newer version. The method depends on how you've installed DeepDive.
hide_description: true
# sitemap: false
---

This chapter shows how to upgrade DeepDive to a newer version. The method depends on how you've installed DeepDive.

0. this unordered seed list will be replaced by toc as unordered list
{:toc}

Before upgrading to v7+, make sure you've read the [CHANGELOG](../CHANGELOG.md){:.heading.flip-title},
especially the part about the [license change](../CHANGELOG.md#license-change)!
{:.note}

## Free version

Upgrading the free version of the theme is as easy as running

```bash
bundle update jekyll-theme-deepdive
```

## GitHub Pages

When building on GitHub Pages, upgrading DeepDive is as simple as setting the `remote_theme` key in `_config.yml` to the desired version.

```yml
remote_theme: hydecorp/deepdive@v9.1.6
```

To use the latest version on the `v9` branch on each build, you can use  `hydecorp/deepdive@v9`.

Continue with [Config](config.md){:.heading.flip-title}
{:.read-more}
