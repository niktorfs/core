
// Confirm deletions
$('.delete').on('click', function(){
    return window.confirm('Are you sure you want to delete this?');
});

// User edit
$('.btn.edit-user').on('click', function(e){
    e.preventDefault();

    // Get variables
    var row = $(this).parents('.dataset');
    var id = row.data('id');
    var name = row.data('name');
    var group = row.data('group');

    var modal = $('#editUser');
    $('#inputEditId').val(id);
    $('#inputEditName').prop('disabled', false).val(name);
    $('#inputEditGroup').prop('disabled', false);
    $('#inputEditGroup option[value="' + group + '"]').prop('selected', true);

    // Admin and everyone disable corresponding fields
    if(id < 3){
        $('#inputEditName').prop('disabled', true);
        $('#inputEditGroup').prop('disabled', true);

        if(id < 2){
            $('#inputEditPassword').prop('disabled', true);
        }
    }

    modal.modal('show');
});

// Group edit
$('.btn.edit-group').on('click', function(e){
    e.preventDefault();

    // Get variables
    var row = $(this).parents('.dataset');
    var id = row.data('id');
    var name = row.data('name');

    var modal = $('#editGroup');
    $('#inputEditId').val(id);
    $('#inputEditName').val(name);

    modal.modal('show');
});

// Permissions toggle
$('.permissions').hide();
$('.edit-permissions').on('click', function(e){
    $(this).next('.permissions').toggle();
});

// Tooltips
$('.hover-help').tooltip();
$('.hover-help').on('click', function(e){ e.preventDefault(); });

// Add dataset
$('.btn-add-dataset').on('click', function(e){
    e.preventDefault();

    // Get form variables (of active tab)
    var tab_pane = $('.tab-pane.active');
    var form = $('form.add-dataset', tab_pane);

    var mediatype = tab_pane.data('mediatype');

    // Loop through fields
    var data = new Object();
    var collection = '';
    $('input', form).each(function(){
        if($(this).attr('name')){
            if($(this).attr('name') == 'collection'){
                collection = $(this).val();
            }else{
                // Regular fields
                if($(this).attr('type') == 'checkbox'){
                    data[$(this).attr('name')] = $(this).attr('checked') ? 1 : 0;
                }else{
                    data[$(this).attr('name')] = $(this).val();
                }
            }
        }
    });

    // Ajax call
    $.ajax({
        url: baseURL + 'api/definitions/' + collection,
        data: JSON.stringify(data),
        method: 'PUT',
        headers: {
            'Content-Type': 'application/tdt.' + mediatype,
            'Authorization': authHeader
        },
        success: function(e){
            // Done, redirect to datets page
            window.location = baseURL + 'api/admin/datasets';
        },
        error: function(e){
            var error = JSON.parse(e.responseText);
            if(error.error && error.error.message){
                $('.error', tab_pane).removeClass('hide').html(error.error.message).show().focus();
            }
        }
    })

});

// Add dataset
$('.btn-edit-dataset').on('click', function(e){
    e.preventDefault();

    // Get form variables
    var form = $('form.edit-dataset');
    var mediatype = form.data('mediatype');
    var identifier = form.data('identifier');

    // Loop through fields
    var data = new Object();
    var collection = '';
    $('input', form).each(function(){
        if($(this).attr('name')){
            if($(this).attr('type') == 'checkbox'){
                data[$(this).attr('name')] = $(this).attr('checked') ? 1 : 0;
            }else{
                data[$(this).attr('name')] = $(this).val();
            }
        }
    });

    // Ajax call
    $.ajax({
        url: baseURL + 'api/definitions/' + identifier,
        data: JSON.stringify(data),
        method: 'POST',
        headers: {
            'Authorization': authHeader
        },
        success: function(e){
            // Done, redirect to datets page
            window.location = baseURL + 'api/admin/datasets';
        },
        error: function(e){
            var error = JSON.parse(e.responseText);
            if(error.error && error.error.message){
                $('.error').removeClass('hide').html(error.error.message).show().focus();
            }
        }
    })

});