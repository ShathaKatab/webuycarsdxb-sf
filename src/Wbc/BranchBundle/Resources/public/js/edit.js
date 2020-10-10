/**
 * Created by majid on 4/14/17.
 */
$(document).ready(function () {
    var makeSelect = $("select[name$='vehicleMake]']");
    if (!makeSelect.length){
        makeSelect = $("select[name$='make]']");
    }

    var modelSelect = $("select[name$='vehicleModel]']");
    if (!modelSelect.length){
        modelSelect = $("select[name$='model]']");
    }

    var modelTypeSelect = $("select[name$='vehicleModelType]']");
    if (!modelTypeSelect.length){
        modelTypeSelect = $("select[name$='modelType]']");
    }

    var transmissionSelect = $("select[name$='vehicleTransmission]']");
    if (!transmissionSelect.length){
        transmissionSelect = $("select[name$='transmission]']");
    }

    var specificationsSelect = $("select[name$='vehicleSpecifications]']");
    if (!specificationsSelect.length){
        specificationsSelect = $("select[name$='specifications]']");
    }

    var transmissionSelector2 = transmissionSelect.prev($('.select2-container')).find($('span.select2-chosen'));
    var specificationSelector2 = specificationsSelect.prev($('.select2-container')).find($('span.select2-chosen'));
    var modelTypeSelector2 = modelTypeSelect.prev($('.select2-container')).find($('span.select2-chosen')).text('');
    var branchSelect = $("select[name$='branch]']");
    var dateBookedContainer = $("div[id$='dateBooked']");
    var branchTimingSelect = $("select[name$='branchTiming]']");
    var branchTimingSelector2 = branchTimingSelect.prev($('.select2-container')).find($('span.select2-chosen'));

    var isValuationConfiguration = window.location.href.includes('valuationconfiguration');

    if(modelTypeSelect.val() && !modelTypeSelector2.text()){
        modelTypeSelector2.text(modelTypeSelect.find("option[value="+modelTypeSelect.val()+"]").text());
    }

    if(isValuationConfiguration){
        modelSelect.show();
        modelSelect.removeAttr('required');
        modelSelect.hide();
    }

    makeSelect.on('change', function () {
        clearTransmissionAndSpecifications();
        var makeIdValue = this.value;
        modelSelect.find('option').remove();
        modelSelect.prev($('.select2-container')).find($('span.select2-chosen')).text('');
        modelTypeSelector2.text('');

        if (makeIdValue) {
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });

            $.get('/admin/wbc/branch/appointment/modelsByMake/' + makeIdValue, function (data) {
                var items = JSON.parse(data);

                $.each(items, function (i, item) {
                    modelSelect.append($('<option>', {
                        value: item.id,
                        text: item.name
                    }));
                });
            }).fail(function () {
                alert('Failed to fetch Vehicle Models');
            }).always(function () {
                $.unblockUI();
            });
        }
    });

    modelSelect.on('change', function (e) {
        if(window.location.href.includes('vehicle/modeltype')){
            e.stopPropagation();
            return;
        }
        clearTransmissionAndSpecifications();
        var modelIdValue = this.value;
        modelTypeSelect.find('option').remove();
        modelTypeSelector2.text('');

        if (modelIdValue) {
            $.blockUI({
                css: {
                    border: 'none',
                    padding: '15px',
                    backgroundColor: '#000',
                    '-webkit-border-radius': '10px',
                    '-moz-border-radius': '10px',
                    opacity: .5,
                    color: '#fff'
                }
            });

            $.get('/admin/wbc/branch/appointment/modelTypesByModel/' + modelIdValue, function (data) {
                var items = JSON.parse(data);

                $.each(items, function (i, item) {
                    modelTypeSelect.append($('<option>', {
                        value: item.id,
                        text: item.name,
                        'data-transmission': item.transmission,
                        'data-body-type': item.body_type,
                        'data-engine': item.engine,
                        'data-gcc': item.is_gcc
                    }));
                });
            }).fail(function () {
                alert('Failed to fetch Vehicle Model Types');
            }).always(function () {
                $.unblockUI();
            });
        }
    });

    modelTypeSelect.on('change', function(){
        clearTransmissionAndSpecifications();
        var selectedOption = modelTypeSelect.find('option[value="'+this.value+'"]');
        var transmission = selectedOption.data('transmission').toLowerCase();
        var isGcc = selectedOption.data('gcc');

        if(transmission != undefined){
            if(transmission != 'manual'){
                transmission = 'automatic';
            }

            transmissionSelect.find('option[value="' + transmission + '"]').prop('selected', true);
            transmissionSelector2.text((transmission.charAt(0).toUpperCase() + transmission.slice(1)));
        }

        if(isGcc != undefined){
            if(isGcc == true){
                specificationsSelect.find('option[value="gcc"]').prop('selected', true);
                specificationSelector2.text('GCC');
            }
        }
    });

    branchSelect.on('change', function(){
        branchTimingSelector2.text('');
        fetchBranchTimings();
    });

    dateBookedContainer.on('change', function(e){
        branchTimingSelector2.text('');
        fetchBranchTimings();
        e.stopPropagation();
    });

    function fetchBranchTimings(){
        var branchId = branchSelect.val();
        var dateBooked = new Date(dateBookedContainer.find("input[name$='dateBooked]']").val());

        if(!branchId || dateBooked == 'Invalid Date' || Object.prototype.toString.call(dateBooked) !== "[object Date]"){
            return;
        }

        var dateString = (new Date(dateBooked - dateBooked.getTimezoneOffset() * 60000))
            .toISOString().split('T')[0];

        branchTimingSelect.find('option').remove();
        branchTimingSelector2.text('');

        $.blockUI({
            css: {
                border: 'none',
                padding: '15px',
                backgroundColor: '#000',
                '-webkit-border-radius': '10px',
                '-moz-border-radius': '10px',
                opacity: .5,
                color: '#fff'
            }
        });

        $.get('/admin/wbc/branch/appointment/branchTimings/' + branchId + '/'+ dateString, function (data) {
            var items = JSON.parse(data);

            if(!items.length){
                var div = $('<div>', {
                    'class': 'alert alert-danger alert-dismissable wbc-alert',
                    text: 'No Branch Timings for selected Branch & Date combination!'
                });
                div.append($('<button>', {
                    type: 'button',
                    'class': 'close',
                    'data-dismiss': 'alert',
                    'aria-hidden': true,
                    text: 'x'
                }));
                $('section.content').prepend(div);

                window.setTimeout(function(){
                    $('.wbc-alert').remove();
                }, 10000);

                return;
            }

            $('.wbc-alert').remove();

            $.each(items, function (i, item) {
                branchTimingSelect.append($('<option>', {
                    value: item.id,
                    text: item.name
                }));
            });
        }).fail(function () {
            alert('Failed to fetch Branch Timings');
        }).always(function () {
            $.unblockUI();
        });
    }

    function clearTransmissionAndSpecifications(){
        transmissionSelector2.text('');
        specificationSelector2.text('');
    }
});