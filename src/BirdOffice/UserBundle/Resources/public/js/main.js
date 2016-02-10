


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
                    managedBy: $('#fos_user_registration_form_manager').val()
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
                managedBy: $('#fos_user_profile_form_manager').val()
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
$( document ).ready(function() {    // Affiche la liste des collaborateurs pour la HP super_admin

    // date PICKER
    $(".date-picker").datepicker().on("change", function () {
        var id = $(this).attr("id");
        var val = $("label[for='" + id + "']").text();
        $("#msg").text(val + " changed");
    });

    $(".datepicker-month").datepicker({
        format: "mm-yyyy",
        startView: "months",
        viewMode: "months",
        minViewMode: "months"
        });
});


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


                // VALIDATION DE LA DEMANDE
                $(function () {

                    $('.validate').change(function () {
                        if ($(this).is(":checked")) {
                            var validation = 1;
                                $('.day-validation').html('Validé');
                        } else {
                            validation = 0;
                          //  $('.day-validation').html('Non Validé');
                        }


                        var validationPath = Routing.generate('validationAjax');

                        ValidationDay();

                        function ValidationDay() {
                            $.ajax({
                                type: "POST",
                                url: validationPath,
                                data: {
                                    dayId: $('.detail-day').attr('data-day'),
                                    validation: validation
                                },
                                success: function () {
                                }
                            });
                        }
                    })
                });
                // FIN VALIDATION
            }
        });
    }


    $(function () {
        var month = $("#select").val();
        var user = $('#username').attr('data-user');
        $('.form-submit').click(function () {
            addNewDay();
            ajaxMonthCall(user, month)
        });
    });


    function addNewDay() {
        var add_new_day = Routing.generate('addNewDay');
        var user = $('#username').attr('data-user');
        $.ajax({
            type: "POST",
            url: add_new_day,
            data: {
                userId: user,
                startDate: $('#date-picker-3').datepicker().val(),
                endDate: $('#date-picker-4').datepicker().val(),
                hours: $('#form-hours').val(),
                absenceType: $('#absence-type').val(),
                presenceType: $('#presence-type').val(),
                description: $('#day-description').val()
            },
            success: function (data) {

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

    //


    function show_day_modal(dayId) {
        var showDayModal = Routing.generate('showDayModal');
        $.ajax({
            type: "POST",
            url: showDayModal,
            data: {
                day: dayId
            },
            success: function (data) {
                console.log(data);
                $('#modal-title').html(data.modalTitle);
                $('#modal-body').html(data.modalBody);
                $('#modal').modal('show');

                $('#edit-day-ajax').click(function(){
                    edit_day(dayId);
                    // return false;
                });
            }
        });
    }
