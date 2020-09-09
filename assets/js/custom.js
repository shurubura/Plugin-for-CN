jQuery(function ($) {
  	$(document).ready(function() {
//Add search for table
  		$('#tasks_table').parent().prepend('<div class="col-sm-12 col-md-6"></div><div class="col-sm-12 col-md-6"><div id="dtBasicExample_filter" class="dataTables_filter"><label for="search-tasks">Search:</label><input id="search-tasks" type="search" class="form-control form-control-sm" placeholder="" aria-controls="dtBasicExample"></div></div>')
  		$("#search-tasks").on("keyup", function() {
		    var value = $(this).val().toLowerCase();
		    $("tbody tr").filter(function() {
		      $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
		    });
		  });

//Add styles to pages
	$('head').append('<link rel="stylesheet" href="'+ fileProp.fileurl + 'assets/css/style.css" type="text/css" />');
//Add sort for table

$(function () {
  $('table')
    .on('click', 'th', function () {
      var index = $(this).index(),
          rows = [],
          thClass = $(this).hasClass('asc') ? 'desc' : 'asc';

      $('#tasks_table th').removeClass('asc desc');
      $(this).addClass(thClass);

      $('#tasks_table tbody tr').each(function (index, row) {
        rows.push($(row).detach());
      });

      rows.sort(function (a, b) {
        var aValue = $(a).find('td').eq(index).text(),
            bValue = $(b).find('td').eq(index).text();

        return aValue > bValue
             ? 1
             : aValue < bValue
             ? -1
             : 0;
      });

      if ($(this).hasClass('desc')) {
        rows.reverse();
      }

      $.each(rows, function (index, row) {
        $('#tasks_table tbody').append(row);
      });
    });
});

//Add modal pop-up
$("a[href='/add_new']").attr("data-toggle", "modal");
$("a[href='/add_new']").attr("data-target", "#myModal");
$("a[href='/add_new']").attr("href", "");

$(".tasks-form").submit(function(e) {
    e.preventDefault();
});



  })
 })