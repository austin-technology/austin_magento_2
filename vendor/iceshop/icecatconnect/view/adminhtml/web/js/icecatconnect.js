require(['jquery', 'mage/template', 'jquery/ui', 'mage/translate', 'mage/loader', 'loaderAjax'], function ($) {

    //vars for load data to information page
    var preloader_data = jQuery('#html-body').attr('data-mage-init');
    var obj = jQuery.parseJSON(preloader_data);
    var preloader_src = null;
    if(obj != null){
        preloader_src = obj.loader.icon;
    }
    var template = '<div id = "custom_loader" class="loading-mask" data-role="loader_custom" style="display: block;"><div class="popup popup-loading"><div class="popup-inner"><img alt="Loading..." src="{src_url}">Please wait...</div></div></div>';
    var preloader_template = template.replace('{src_url}', preloader_src);
    var refresh_url = false;
    // var button = '<a href="javascript:void(0)" id="reload_button" title="Reload" type="button" class=" action-default page-actions-buttons"><span class=""><span>Reload</span></span></a>';
    var button = '<button id="reload_button_icecatconnect" title="Reload" type="button" class="primary"><span class=""><span>Reload</span></span></button>';

    //other vars

    var prefix = 'language_field_for_store_view_';
    var stores = jQuery('#iceshop_icecatconnect_icecatconnect_language_mapping_stores');
    var languages_row = jQuery('#row_iceshop_icecatconnect_icecatconnect_language_mapping_languages_values');

    if (languages_row.length > 0) {
        var block = jQuery('#row_iceshop_icecatconnect_icecatconnect_language_mapping_languages_values').parents('div.section-config.icecatconnect_group').find('fieldset.config').find('tbody');
        if (stores.length > 0) {
            stores.find('option').each(function () {
                var label = jQuery(this).html();
                var id = prefix + jQuery(this).val();
                // var id = jQuery(this).val();
                var languages_row_clone = languages_row.clone();
                var row_id = 'row_iceshop_icecatconnect_icecatconnect_language_mapping_' + id;

                languages_row_clone.attr('id', row_id);
                languages_row_clone.addClass('row_magento_store_views');
                languages_row_clone.find('td.label').find('label').attr('for', id);
                languages_row_clone.find('td.label').find('label').find('span').html(label);
                languages_row_clone.find('td.value').find('select').attr('id', id);
                languages_row_clone.find('td.value').find('select').attr('name', id);
                languages_row_clone.find('td.value').find('select').attr('data-ui-id', '');
                languages_row_clone.find('td.value').find('select').addClass('magento_store_views');
                block.append(languages_row_clone);
            });

            jQuery('#row_iceshop_icecatconnect_icecatconnect_language_mapping_stores').remove();
            jQuery('#row_iceshop_icecatconnect_icecatconnect_language_mapping_languages_values').remove();
        }
    }

    multilingualFields();
    setValuesForStores();
    collectAllLanguages();

    jQuery('body').on('change', '.magento_store_views', function () {
        collectAllLanguages();
    });

    jQuery('body').on('change', '#iceshop_icecatconnect_icecatconnect_language_mapping_multilingual_mode', function () {
        multilingualFields();
    });


    function collectAllLanguages()
    {
        var lanugagesObject = {};
        var languages_fields = jQuery('.magento_store_views');
        if (languages_fields.length > 0) {
            languages_fields.each(function () {
                var tmp = jQuery(this).attr('id').replace(prefix, '');
                // lanugagesObject[tmp] = jQuery(this).val();
                lanugagesObject[tmp] = {
                    'store_id': tmp,
                    'value': jQuery(this).val(),
                };
            });

            jQuery('#iceshop_icecatconnect_icecatconnect_language_mapping_multilingual_values').val(JSON.stringify(lanugagesObject));
        }
        setValuesForStores();
    }

    function setValuesForStores()
    {
        var element = jQuery('#iceshop_icecatconnect_icecatconnect_language_mapping_multilingual_values');
        if (element.length > 0) {
            var data = element.val();
            if (data != '') {
                var parse = JSON.parse(data);
                for (var key in parse) {
                    $('#' + prefix + key).val(parse[key]['value']);
                }
            }
        }
    }

    function multilingualFields()
    {
        var mode = jQuery('#iceshop_icecatconnect_icecatconnect_language_mapping_multilingual_mode').val();
        if (mode == 1) {
            jQuery('.row_magento_store_views').show();
        } else {
            jQuery('.row_magento_store_views').hide();
        }
    }


    function getICETabsContent()
    {
        //check url for send ajax request
        var check_existing_url = jQuery("#iceshop_icecatconnect_information_icecatconnect_information_path_url").length;

        if (check_existing_url > 0) {
            jQuery('#html-body').append(preloader_template);
            jQuery(document).ready(function () {
                var url = jQuery("#iceshop_icecatconnect_information_icecatconnect_information_path_url").find('option')[0].value;
                //ajax request
                refresh_url = url;
                $.ajax({
                    url: url,
                    type: 'get',
                    dataType: 'json',
                    context: jQuery('#html-body'),
                    // showLoader: true
                }).done(function (response) {
                    for (var key in response) {
                        jQuery("#iceshop_icecatconnect_information_" + key).find('tbody').html(response[key]);
                    }
                    var has_problems = jQuery('.has_problems').length;
                    /*if (has_problems > 0) {
                        jQuery('.iceanalyzer_problems_digest').css('display', 'block');
                        if (jQuery('.section-config.iceanalyzer_problems_digest').hasClass('active') == false) {
                            // jQuery('.section-config.iceanalyzer_problems_digest').addClass('active')
                            jQuery('#iceshop_iceanalyzer_problems_digest-head').click();
                        }
                    }*/
                    jQuery('#save').remove();
                    jQuery('.page-actions').append(button);
                    jQuery('#custom_loader').remove();
                }).fail(function (response) {
                    alert('There is some error with Ajax request');
                    jQuery('#custom_loader').remove();
                });
            });
        }
    }

    jQuery(document).ready(function () {
        getICETabsContent();
    });

    jQuery(document).on('click', '#reload_button_icecatconnect', function () {
        jQuery('#html-body').append(preloader_template);
        if (refresh_url != false) {
            $.ajax({
                url: refresh_url,
                type: 'get',
                dataType: 'json',
                context: jQuery('#html-body'),
                // showLoader: true
            }).done(function (response) {
                for (var key in response) {
                    jQuery("#iceshop_icecatconnect_information_" + key).find('tbody').html(response[key]);
                }
                jQuery('#custom_loader').remove();
            }).fail(function (response) {
                alert('There is some error with Ajax request');
                jQuery('#custom_loader').remove();
            });
        }
    });


});