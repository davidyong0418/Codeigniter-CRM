<script type="text/javascript">
    (function (window, document, $, undefined) {

        $(function () {

            var total_header = ($('table#DataTables th:last').index());
            var testvar = [];
            for (var i = 0; i < total_header; i++) {
                testvar[i] = i;
            }
            var length_options = [10, 25, 50, 100];
            var length_options_names = [10, 25, 50, 100];

            var tables_pagination_limit =<?= config_item('tables_pagination_limit')?>;
            tables_pagination_limit = parseFloat(tables_pagination_limit);

            if ($.inArray(tables_pagination_limit, length_options) == -1) {
                length_options.push(tables_pagination_limit)
                length_options_names.push(tables_pagination_limit)
            }
            length_options.sort(function (a, b) {
                return a - b;
            });
            length_options_names.sort(function (a, b) {
                return a - b;
            });

            $("[id^=DataTables]").dataTable({
                'paging': true,  // Table pagination
                'responsive': true,  // Table pagination
                "pageLength": tables_pagination_limit,
                "aLengthMenu": [length_options, length_options_names],
                'ordering': true,  // Column ordering
                'dom': 'lBfrtip',  // Bottom left status text
                buttons: [
                    {
                        extend: 'print',
                        text: "<i class='fa fa-print'> </i>",
                        className: 'btn btn-danger btn-xs mr',
                        exportOptions: {
                            columns: [testvar[0], testvar[1], testvar[2], testvar[3], testvar[4], testvar[5]]
                        }
                    },
                    {
                        extend: 'print',

                        text: '<i class="fa fa-print"> </i> &nbsp;<?= lang('selected')?>',
                        className: 'btn btn-success mr btn-xs',
                        exportOptions: {
                            modifier: {
                                selected: true,
                                columns: [testvar[0], testvar[1], testvar[2], testvar[3], testvar[4], testvar[5]]
                            }
                        }

                    },
                    {
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o"> </i>',
                        className: 'btn btn-purple mr btn-xs',
                        exportOptions: {
                            columns: [testvar[0], testvar[1], testvar[2], testvar[3], testvar[4], testvar[5]]
                        }
                    },
                    {
                        extend: 'csv',
                        text: '<i class="fa fa-file-excel-o"> </i>',
                        className: 'btn btn-primary mr btn-xs',
                        exportOptions: {
                            columns: [testvar[0], testvar[1], testvar[2], testvar[3], testvar[4], testvar[5]]
                        }
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o"> </i>',
                        className: 'btn btn-info mr btn-xs',
                        exportOptions: {
                            columns: [testvar[0], testvar[1], testvar[2], testvar[3], testvar[4], testvar[5]]
                        }
                    },
                ],
                select: true,
                // Text translation options
                // Note the required keywords between underscores (e.g _MENU_)
                oLanguage: {
                    sSearch: '<?= lang('search_all_column')?>',
                    sLengthMenu: '_MENU_',
                    info: '<?= lang('showing')?> <?= lang('page')?> _PAGE_ of _PAGES_',
                    zeroRecords: '<?= lang('nothing_found_sorry')?>',
                    infoEmpty: '<?= lang('no_record_available')?>',
                    infoFiltered: '(<?= lang('filtered_from')?> _MAX_ <?= lang('total')?> <?= lang('records')?>)'
                }

            });

        });

    })(window, document, window.jQuery);
</script>