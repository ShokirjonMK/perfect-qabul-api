$(document).ready(function () {

    // user profile UI

    doThis('profile-country_id', 'profile-region_id', 'profile-permanent_place_id');
    doThis('profile-temporary_country_id', 'profile-temporary_region_id', 'profile-temporary_place_id');
    doThis('profile-birth_country_id', 'profile-birth_region_id', 'profile-birth_place_id');

    function doThis(dd_country_id, dd_region_id, dd_district_id){
    
        $('#' + dd_country_id).change(function(){
            $('#' + dd_region_id).html('');
            var dd = $(this);
            var country_id = dd.val();
            if(country_id != ''){
                var url = $("#url-regions").attr('data-url');
                var params = "?country_id=" + country_id;
                $.ajax({
                    url: url + params,
                    success: function(data){
                        $.each(data, function(id, value){
                            $('#' + dd_region_id).append(`<option value="${id}">${value}</option>`);
                        });
                        $('#' + dd_region_id).trigger("change");
                    }
                });
            }
        });
    
        $('#' + dd_region_id).change(function(){
            $('#' + dd_district_id).html('');
            var dd = $(this);
            var region_id = dd.val();
            if(region_id != ''){
                var url = $("#url-districts").attr('data-url');
                var params = "?region_id=" + region_id;
                $.ajax({
                    url: url + params,
                    success: function(data){
                        $.each(data, function(id, value){
                            $('#' + dd_district_id).append(`<option value="${id}">${value}</option>`);
                        });
                    }
                });
            }
            
        });

    }

    // ****


    // user work UI

    $('#employee-department_id').change(function(){
        $('#employee-job_id').html('');
        $('#employee-job_id').append(`<option value="">...</option>`);
        var dd = $(this);
        var department_id = dd.val();
        if(department_id != ''){
            var url = $("#url-jobs").attr('data-url');
            var params = "?department_id=" + department_id;
            $.ajax({
                url: url + params,
                success: function(data){
                    $.each(data, function(id, value){
                        $('#employee-job_id').append(`<option value="${id}">${value}</option>`);
                    });
                }
            });
        }
    });

    // ****

    // location(city) UI

    $('#regions-country_id').change(function(){
        $('#regions-parent_id').html('');
        $('#regions-parent_id').append(`<option value="">...</option>`);
        var dd = $(this);
        var country_id = dd.val();
        if(country_id != ''){
            var url = $("#url-regions").attr('data-url');
            var params = "?country_id=" + country_id;
            $.ajax({
                url: url + params,
                success: function(data){
                    $.each(data, function(id, value){
                        $('#regions-parent_id').append(`<option value="${id}">${value}</option>`);
                    });
                }
            });
        }
    });

    // ****



    // user subjects UI

    $('.btn-add-option').on('click', function (){
        $('#tbl-subjects tr:last').after('<tr class="tr_item">' + $('#template').html() + '</tr>');

        var select_attr = $('#tbl-subjects tr:last td:first select');

        select_attr.addClass('select-subject select-two');

        var selected_data = JSON.parse($("#selected_data").val());
        var subjects = [];
        $.each(selected_data, function(id, row){
            subjects.push(row.subject)
        });
        
        $.each(select_attr.find('option'), function(id, row){
            var option = $(this);
            if(subjects.includes($(this).val())){
                option.remove();
            }
        });

        select_attr.select2();

        var select_option = $('#tbl-subjects tr:last').find("td:eq(1)").find("select")
        select_option.addClass('select-lang select-two');
        select_option.select2();

    });

    $('#tbl-subjects').on('click','.btn-remove-option', function (){
        $(this).parent().parent().parent().remove();
        eventChange();
    });

    $('#tbl-subjects').on('change','.select-subject', function (){
        
        eventChange();

    });

    $('#tbl-subjects').on('change','.select-lang', function (){
        
        eventChange();

    });

    function eventChange(){
        console.clear();
        console.log("___________________________________");
        var selected_data = [];

        $( "#tbl-subjects tr.tr_item" ).each(function( index ) {
            var tr = $(this);

            var select_subject =  tr.find(".select-subject");
            var selected_subject_val = select_subject.val();

            var select_lang =  select_subject.parent().parent().find(".select-lang");
            var selected_lang_val = select_lang.val();
            
            var obj = {};
            obj['subject'] = selected_subject_val;
            obj['langs'] = selected_lang_val;
            selected_data.push(obj);
          });
          console.log( JSON.stringify(selected_data));
          $("#selected_data").val(JSON.stringify(selected_data));
    }

    $('.select-two').select2();
    $('.select-subject').trigger('change');

    // ***

});