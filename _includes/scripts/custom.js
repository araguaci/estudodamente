/* global $, DOMPurify */
/*!
 * Custom Scripts
 * Copyright (c) 2023 JV conseil <https://www.jv-conseil
 */

!(function () {
  'use strict';
  /**
   * Add target="'_blank" to all external links
   */
  $("a[href^='http']").each(function () {
    /* console.debug('_link', this.href) */
    $(this).attr('target', '_blank')
  });


  /**
   * mailto click
   */
  $('a[href^="mailto:"]').on('click', function () {
    /* window.location = 'mailto:' + encodeURIComponent($(this).data('mail')) + '?subject=' + encodeURIComponent($(this).data('subject')) + '&body=' + encodeURIComponent("Hello " + SITE.title + ",\n\n...\n\nKind Regards,\n") */

    let email = $(this).attr('href').trim()
    email = DOMPurify.sanitize(email)
    email = email.replace(/mailto:/g, '')

    // console.debug("email ", email)

    let link = 'mailto:' + encodeURIComponent(email)
    link += '?subject=' + encodeURIComponent("Sent From JV conseil Website")
    link += '&body=' + encodeURIComponent("Hello JV conseil,\n\n...\n\nKind Regards,\n")

    // console.debug("link ", link)

    window.location = link

    // event.preventDefault()
    return false
  })
})()
