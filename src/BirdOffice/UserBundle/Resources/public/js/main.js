$( document ).ready(function() {    // Affiche la liste des collaborateurs pour la HP super_admin
    $(function () {
        $('#manager').change(function () {
            ajaxCall();
        });
        ajaxCall();
    });

    var urlpost = Routing.generate('ajaxList');

    function ajaxCall() {

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

    var showAddModal = Routing.generate('showAddModal');

    $('#add-partner').click(function () {
        show_add_modal();
    });

    function show_add_modal(userId) {
        $.ajax({
            type: "POST",
            url: showAddModal,
            data: {
                user: userId
            },
            success: function (data) {
                $('#modal-title').html(data.modalTitle);
                $('#modal-body').html(data.modalBody);
                $('#modal').modal('show');
            }
        })
    }

    // Modale ajout collaborateur
    var addPartnerAjax = Routing.generate('addPartnerAjax');

    function ajax_add_partner() {
        $.ajax({
            type: "POST",
            url: addPartnerAjax,
            data: {
                civility: $('#fos_user_registration_form_civility').val(),
                name: $('#fos_user_registration_form_name').val(),
                username: $('#fos_user_registration_form_username').val(),
                email: $('#fos_user_registration_form_email').val(),
                plainPassword: $('#fos_user_registration_form_plainPassword_first').val(),
                managedBy: $('#fos_user_registration_form_managedBy').val()
            },
            success: function (data) {
                $('#modal-title').html(data.modalTitle);
                $('#modal-body').html(data.modalBody);
                $('#modal').modal('show');
            }
        });
    }

    // Modale édition collaborateur
    var editUser = Routing.generate('editPartnerAjax');

    function ajax_edit_partner(userId) {
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
            }
        });
    }

    function trashWarningModal(userId) {
        $('#modal2').modal('show');
        $("#modal-delete").attr('data-user', userId);
    }

    //  suppression collaborateur
    var deleteUser = Routing.generate('deletePartnerAjax');

    function ajax_delete_partner(userId) {
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

    // DATE PICKER
    $(".date-picker").datepicker().on("change", function () {
        var id = $(this).attr("id");
        var val = $("label[for='" + id + "']").text();
        $("#msg").text(val + " changed");
    });


    //Affiche la liste des absences/présences
    $(function () {
        var month = $("#select").val();
        var user = $('#username').attr('data-user');

        $('.month').change(function () {
            var month = $("#select").val();
            ajaxMonthCall(user, month);
        });
        ajaxMonthCall(user, month);
    });

    var add_presence_absence = Routing.generate('addPresenceAbsence');

    function ajaxMonthCall(user, month) {
        $.ajax({
            type: "POST",
            url: add_presence_absence,
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
                            $('.day-validation').html('Non Validé');
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
            return false;
            ajaxMonthCall(user, month)
        });
    });

    var add_new_day = Routing.generate('addNewDay');
    var user = $('#username').attr('data-user');

    function addNewDay() {
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

    var detailDayAjax = Routing.generate('ModalDetailContent');

    function detailDay(dayId) {
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
});