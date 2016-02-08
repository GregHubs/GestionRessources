/////////////SUPER ADMIN INDEX//////////////////////

//Affiche la liste des collaborateurs
$(function (){
    $('#manager').change(function(){
        ajaxCall();
    });
    ajaxCall();
});

var urlpost = "{{  path('ajaxList') }}";

function ajaxCall(){

    $.ajax({
        type: "POST",
        url: urlpost,
        data: {
            manager: $('#manager').val()
        },
        success: function (msg) {
            $('#searchContainer').html(msg);

            $(".edit-user").unbind('click').click(function(){
                ajax_edit_partner(
                    $(this).attr('data-user')
                );
            });

            $(".delete-user").click(function(){
                ajax_delete_partner(
                    $(this).attr('data-user')
                );
            });

            //  trash warning modal
            $('.trash-user').click(function (){
                trashWarningModal(
                    $(this).attr('data-user')
                );
            });
        }
    });

}

// Show add modal

var showAddModal = "{{ path('showAddModal') }}";

$('#add-partner').click(function (){
    show_add_modal();
});

function show_add_modal(userId){
    $.ajax({
        type: "POST",
        url: showAddModal,
        data: {
            user: userId
        },
        success: function(data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');
        }
    })}

// Modale ajout collaborateur
var addPartnerAjax = "{{  path('addPartnerAjax') }}";

function ajax_add_partner(){
    $.ajax({
        type: "POST",
        url: addPartnerAjax,
        data: {
            civility:$('#fos_user_registration_form_civility').val(),
            name:$('#fos_user_registration_form_name').val(),
            username:$('#fos_user_registration_form_username').val(),
            email:$('#fos_user_registration_form_email').val(),
            plainPassword:$('#fos_user_registration_form_plainPassword_first').val(),
            managedBy:$('#fos_user_registration_form_managedBy').val()
        },
        success: function(data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');
        }
    });
}

// Modale édition collaborateur
var editUser = "{{ path('editPartnerAjax') }}";

function ajax_edit_partner(userId){
    $.ajax({
        type: "POST",
        url: editUser,
        data: {
            user: userId
        },
        success: function(data) {
            console.log(data);
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');
        }
    });
}

function trashWarningModal(userId){
    $('#modal2').modal('show');
    $("#modal-delete").attr('data-user', userId);
}

//  suppression collaborateur
var deleteUser = "{{ path('deletePartnerAjax') }}";

function ajax_delete_partner(userId){
    $.ajax({
        type: "POST",
        url: deleteUser,
        data: {
            user: userId
        },
        success: function(data) {
            if(data.responseCode == 200){
                $.notify(data.message, data.notification);
                $('#searchContainer').html(data.htmlContent);
            }
        }
    })}



///////////////////// COLLABORATEUR ///////////////////////

// DATE PICKER
$(".date-picker").datepicker();

$(".date-picker").on("change", function () {
    var id = $(this).attr("id");
    var val = $("label[for='" + id + "']").text();
    $("#msg").text(val + " changed");
});


//Affiche la liste des absences/présences
$(function (){
    var month = $("#select").val();
    var user = $('#username').attr('data-user');

    $('.month').change(function(){
        var month = $("#select").val();
        ajaxMonthCall(user, month);
    });
    ajaxMonthCall(user, month);
});

var add_presence_absence = "{{  path('add_presence_absence') }}";

function ajaxMonthCall(user, month){

    $.ajax({
        type: "POST",
        url: add_presence_absence,
        data: {
            month: month,
            userId: user
        },
        success: function (data) {
            $('#presences').html(data);
        }
    });

}
