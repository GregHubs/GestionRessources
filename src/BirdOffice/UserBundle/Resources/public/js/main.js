function ajaxCall() {
    var urlpost = Routing.generate('ajaxList');

    $.ajax({
        type: "POST",
        url: urlpost,
        data: {
            manager: $('#manager').val()
        },
        success: function (msg) {
            $('#searchContainer').html(msg);

            $(".edit-user").unbind('click').click(function () {
                ajax_edit_partner(
                    $(this).attr('data-user')
                );
            });


            $(".delete-user").click(function () {
                ajax_delete_partner(
                    $(this).attr('data-user')
                );
            });

            //  trash warning modal
            $('.trash-user').click(function () {
                trashWarningModal(
                    $(this).attr('data-user')
                );
            });
        }
    });

}

// Show add modal

function show_add_modal() {
    var showAddModal = Routing.generate('showAddModal');
    $.ajax({
        type: "POST",
        url: showAddModal,
        data: {
        },
        success: function (data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');

            $('#add-partner-ajax').unbind('click').click(function(){
                ajax_add_partner();
                //return false;
            });
        }
    })
}

// validation formulaire

function validateForm(){
    if ($('#form_presenceType').val() != '' &&  $('#form_absenceType').val() != '' ) {
        //alert("Absence et présence ne peuvent être remplis en même temps");
        $('.error-field').css('display','block');
        return false;
    }
    if($('#form_startDate_day').val() != '' && $('#form_andDate_day').val() != '' &&  $('#form_hours').val() != 0 ){
        $('.error-field-2').css('display','block');
        return false;
    }
    return true;
}




// Modale ajout collaborateur


function ajax_add_partner() {
    var addPartnerAjax = Routing.generate('addPartnerAjax');

    $.ajax({
        type: "POST",
        url: addPartnerAjax,
        data: {
            civility: $('#fos_user_registration_form_civility').val(),
            lastname: $('#fos_user_registration_form_lastname').val(),
            firstname: $('#fos_user_registration_form_firstname').val(),
            email: $('#fos_user_registration_form_email').val(),
            plainPassword: $('#fos_user_registration_form_plainPassword_first').val(),
            manager: $('#fos_user_registration_form_manager').val()
        },
        success: function (data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');
            if (data.responseCode == 200) {
                $.notify(data.message, data.notification);
            }
        }
    });
}


// Affiche Modale édition collaborateur

function ajax_edit_partner(userId) {
    var editUser = Routing.generate('editPartnerAjax');

    $.ajax({
        type: "POST",
        url: editUser,
        data: {
            user: userId
        },
        success: function (data) {
            console.log(data);
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');

            $('#edit-partner-ajax').click(function(){
                edit_partner(userId);
                // return false;
            });
        }
    });
}

// traitement édition partenaire
function edit_partner(userId) {
    var addPartnerAjax = Routing.generate('editPartner');

    $.ajax({
        type: "POST",
        url: addPartnerAjax,
        data: {
            userId:userId,
            civility: $('#fos_user_profile_form_civility').val(),
            lastname: $('#fos_user_profile_form_lastname').val(),
            firstname: $('#fos_user_profile_form_firstname').val(),
            email: $('#fos_user_profile_form_email').val(),
            plainPassword: $('#fos_user_profile_form_plainPassword_first').val(),
            manager: $('#fos_user_profile_form_manager').val()
        },
        success: function (data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');
            if (data.responseCode == 200) {
                $.notify(data.message, data.notification);
            }
        }
    });
}

function trashWarningModal(userId) {
    $('#modal2').modal('show');
    $("#modal-delete").attr('data-user', userId);
}

//  suppression collaborateur

function ajax_delete_partner(userId) {
    var deleteUser = Routing.generate('deletePartnerAjax');

    $.ajax({
        type: "POST",
        url: deleteUser,
        data: {
            user: userId
        },
        success: function (data) {
            if (data.responseCode == 200) {
                $.notify(data.message, data.notification);
                $('#searchContainer').html(data.htmlContent);
            }
        }
    })
}

// Affiche la liste des collaborateurs pour la HP super_admin

function ajaxMonthCall(user, month) {
    var monthCall = Routing.generate('monthCallAjax');

    $.ajax({
        type: "POST",
        url: monthCall,
        data: {
            month: month,
            userId: user
        },
        success: function (data) {
            $('#presences').html(data);

            $('.detail-day').unbind('click').click(function () {
                detailDay($(this).attr('data-day'));
            });

            $(".edit-day").unbind('click').click(function () {
                ShowEditDay(
                    $(this).attr('data-day'),
                    $(this).attr('data-user')
                );
            });
            // VALIDATION DE LA DEMANDE
            $(function () {
                $('.validate-status').change(function () {
                    var status = $(this).val();
                    var day = $(this).parent().data('day');
                    ValidationDay(status, day);
                });
            });
            // FIN VALIDATION
        }
    });
}

function ValidationDay(status, day) {
    $.ajax({
        type: "POST",
        url: Routing.generate('validationAjax'),
        data: {
            dayId: day,
            status: status
        },
        success: function (data) {
            if (data.responseCode == 200) {
                $.notify(data.message, data.notification);
            }
        }
    });
}


// Affiche Modale édition jour

function ShowEditDay(dayId, userId) {
    $.ajax({
        type: "POST",
        url: Routing.generate('showEditDay'),
        data: {
            userId: userId,
            dayId:  dayId
        },
        success: function (data) {
            console.log(data);
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');

            $("#edit-day-ajax").unbind('click').click(function () {
                validateForm();
                if(validateForm()){
                    EditDay(dayId, userId);
                    ajaxMonthCall(userId, $("#select-month").val())
                }
                return false;
            });
        }
    });
}

// Modification jour

function EditDay(dayId, userId) {
    $.ajax({
        type: "POST",
        url: Routing.generate('editDay'),
        data: {
            userId: userId,
            dayId: dayId,
            absenceType: $('#form_absenceType').val(),
            presenceType: $('#form_presenceType').val(),
            startDate: $('#form_startDate').val(),
            endDate: $('#form_endDate').val(),
            description: $('#form_description').val(),
            hours: $('#form_hours').val()
        },
        success: function (data) {
            if (data.responseCode == 200) {
                $.notify(
                    data.message,
                    data.notification
                );
            }
        }
    });
}

// Affiche le détail d'une journée dans une modale
function detailDay(dayId) {
    var detailDayAjax = Routing.generate('ModalDetailContent');
    $.ajax({
        type: "POST",
        url: detailDayAjax,
        data: {
            dayId: dayId
        },
        success: function (data) {
            $('#modal-title').html(data.modalTitle);
            $('#modal-body').html(data.modalBody);
            $('#modal').modal('show');

        }
    });
}

