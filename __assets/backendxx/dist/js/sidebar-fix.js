$(function () {
  function fixSidebar() {
    if ($(window).width() <= 768) {
      $('body')
        .addClass('sidebar-collapse')
        .removeClass('sidebar-open');
    } else {
      $('body').removeClass('sidebar-collapse');
    }
  }

  fixSidebar();
  $(window).on('resize', fixSidebar);
});
