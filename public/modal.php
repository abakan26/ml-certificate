<?php ?>
<style>
    .tech-modal {
        display: none;
        position: fixed;
        z-index: 1060;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(255, 245, 245,  0.4)
    }

    .modal-content {
        position: relative;
        background-color: #fefefe;
        margin: 100px auto 0 auto;
        border: 1px solid #888;
        width: 100%;
        max-width: 580px;
        box-sizing: border-box;
    }

    .modal-close {
        color: #000000;;
        position: absolute;
        right: 15px;
        top: 0;
        font-size: 36px;
        font-weight: bold;
    }
    .modal-close:hover, .modal-close:focus {
        color: #F8AF43;
        text-decoration: none;
        cursor: pointer;
    }

    .modal__header {
        padding-top: 50px;
        padding-bottom: 50px;
        max-width: 540px;
        margin: 0 auto;
        text-align: center;
    }

    .modal__title {
        font-weight: bold;
        font-size: 28px;
        line-height: 30px;
        color: #000000;
        margin: 0;
    }

    .modal__text {
        font-style: normal;
        font-weight: 500;
        font-size: 18px;
        line-height: 20px;

        margin: 20px 0 0;
        color: #000000;
    }

</style>
<div class="tech-modal js-modal" id="tech-modal">
    <div class="modal-content"><span class="modal-close js-modal-close">&times;</span>
        <div class="modal__header">
            <p class="modal__title">У нас есть страница часто задаваемых вопросов</p>
            <p class="modal__text">возможно там вы найдете ответ на интересующий вас вопрос</p>
            <a href="https://academy.sppm.su/knowledgebase/" style="margin-top: 15px;color: white" class="btn btn-primary">Перейти на страницу</a>
        </div>
    </div>
</div>
<script>
    window.addEventListener("load", function() {
        var askDropdown = jQuery("#ask-dropdown");
        var mobileAskDropdown = jQuery(".mobile-menu .panel-toggler");
        var closePopupButton = jQuery(".js-modal-close");
        askDropdown.on("click", openPopup)
        mobileAskDropdown.on("click", openPopup)
        closePopupButton.on("click", closePopup)
        function openPopup(event) {
            jQuery('#tech-modal').fadeIn(200, function () {
                document.cookie = 'knowledgebase-page=yes';
                askDropdown.off("click", openPopup);
                askDropdown.trigger('click');
                mobileAskDropdown.off("click", openPopup);
                mobileAskDropdown.trigger('click');
            });
        }
        function closePopup() {
            jQuery('#tech-modal').fadeOut(200, function () {
                askDropdown.trigger('click');
            });
        }
    });
</script>
