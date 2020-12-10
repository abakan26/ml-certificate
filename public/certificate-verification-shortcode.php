<?php
function certificate_verification_callback($atts)
{
    ob_start();
    ?>
    <style>
        body.page:not(.twentyseventeen-front-page) .entry-title {
            font-size: 22px;
        }
        @media (min-width: 768px) {
            body.page:not(.twentyseventeen-front-page) .entry-title {
                font-size: 27px;
            }
        }
        .certificate-search-result th, .certificate-search-result td{
            padding: 0.25rem;
            font-size: 14px;
        }
        .page.page-one-column:not(.twentyseventeen-front-page) #primary {
            max-width: unset;
        }
        .result-fio {
            min-width: 290px;
            font-weight: normal;
        }
        .result-certificate-name{
            max-width: 330px;
        }
    </style>

    <div id="certificateSearchResult"></div>
    <div class="alert alert-success" role="alert" data-status="success" style="display:none;">
        Сертификат существует
    </div>
    <div class="alert alert-warning" role="alert" data-status="warning" style="display:none;">
        Данный не соответствуют ни одному результату
    </div>
    <nav class="mt-3">
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <a class="nav-item nav-link active" id="nav-fio-tab" data-toggle="tab" href="#nav-fio" role="tab"
               aria-controls="nav-fio" aria-selected="true">По ФИО</a>
            <a class="nav-item nav-link" id="nav-series-number-tab" data-toggle="tab" href="#nav-series-number"
               role="tab"
               aria-controls="nav-series-number" aria-selected="false">По серии и номеру</a>
        </div>
    </nav>
    <div class="tab-content pt-3" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-fio" role="tabpanel" aria-labelledby="nav-fio-tab">
            <form class="certificateForm">
                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="graduate_last_name">
                                Фамилия
                                <small id="passwordHelpInline" class="text-danger">
                                    * (обязательное поле)
                                </small>
                            </label>
                            <input required type="text" class="form-control" id="graduate_last_name"
                                   name="graduate_last_name">

                        </div>
                        <div class="form-group">
                            <label for="graduate_first_name">
                                Имя
                                <small id="passwordHelpInline" class="text-danger">
                                    * (обязательное поле)
                                </small>
                            </label>
                            <input required type="text" class="form-control" id="graduate_first_name"
                                   name="graduate_first_name">
                        </div>
                        <div class="form-group">
                            <label for="graduate_surname">Отчество</label>
                            <input type="text" class="form-control" id="graduate_surname"
                                   name="graduate_surname">
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="action" value="ml_verify_certificate_by_fio">
                        <button class="btn btn-primary" type="submit" name="submit">
                            <span style="display:none;" class="spinner-border spinner-border-sm" role="status"
                                  aria-hidden="true"></span>
                            Проверить
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="tab-pane fade" id="nav-series-number" role="tabpanel" aria-labelledby="nav-series-number-tab">
            <form class="certificateForm">
                <div class="form-row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="series">
                                Серия
                                <small id="passwordHelpInline" class="text-danger">
                                    * (обязательное поле)
                                </small>
                            </label>
                            <input required type="text" class="form-control" name="series" id="series">
                        </div>
                        <div class="form-group">
                            <label for="number">
                                Номер
                                <small id="passwordHelpInline" class="text-danger">
                                    * (обязательное поле)
                                </small>
                            </label>
                            <input required type="text" class="form-control" name="number" id="number">
                        </div>
                    </div>
                    <div class="col-12">
                        <input type="hidden" name="action" value="ml_verify_certificate_by_series">
                        <button class="btn btn-primary" type="submit" name="submit">
                            <span class="spinner-border spinner-border-sm" style="display:none;" role="status"
                                  aria-hidden="true"></span>
                            Проверить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        (function ($) {
            const AJAX_URL = "/wp-admin/admin-ajax.php";
            $(".certificateForm").submit(verifyCertificateFormHandler);
            function verifyCertificateFormHandler(event) {
                event.preventDefault();
                let form = $(this);
                print_result(false, "success");
                print_result(false, "warning");
                $(this).addClass("was-validated");
                if (this.checkValidity() === false) {
                    event.stopPropagation();
                    return false;
                }
                $(this).find("[name=submit]").prop("disabled", true)
                $(this).find("[name=submit]").find("[role=status]").show();
                $.ajax({
                    url: AJAX_URL,
                    method : 'POST',
                    data: $(this).serialize(),
                    success: function (json) {
                        let data = JSON.parse(json);
                        if (data.status === "success") {
                            print_result(true, "success",  data.message);
                        } else {
                            print_result(true, "warning", data.message);
                        }
                    },
                    complete: function () {
                        form.find("[name=submit]").prop("disabled", false)
                        form.find("[name=submit]").find("[role=status]").hide();
                    }
                });

            }

            function print_result(show, status, message = '') {
                let view = status === "success" ? $("#certificateSearchResult") : $("[data-status="+status+"]");
                if (show) {
                    if (status === "success"){
                        console.log(view)
                        view.html(message)
                        view.show();
                        return view;
                    }
                    view.text(message);
                    view.show();
                } else {
                    view.hide();
                }
                return view;
            }
        })(jQuery);
    </script>
    <?php
    return ob_get_clean();
}
