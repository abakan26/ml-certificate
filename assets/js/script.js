
jQuery(document).ready(() => {
    let media = wp.media({
        multiple: false
    });
    media.on( 'close', function (){
        let attachment = media.state().get('selection').first();
        if(attachment !== undefined){
            jQuery(".alert").hide();
        }
    });
    media.on('select', function() {
        let attachment = media.state().get('selection').first().toJSON();
        jQuery("[name=attachment_id]").val(attachment.id);
        jQuery(".parent").css("background-image", `url(${attachment.url})`);
    });

    jQuery(document).on('click', 'button[data-target="media-open"]', function() {
        media.open();
    });
});


class Field {
    constructor(props) {
        this.root = props.root;
        this.view = props.view;
        this.fontView = this.view.querySelector("[data-view=font]");
        this.textView = this.view.querySelector("[data-view=text]");
        this.sizeView = this.view.querySelector("[data-view=size]");
        this.positionView = this.view.querySelector("[data-view=position]");
        this.positionXInpt = this.root.querySelector("[data-position=x]");
        this.positionYInpt = this.root.querySelector("[data-position=y]");
        this.widthInpt = this.root.querySelector("[data-size=width]");
        this.fontSizeInpt = this.root.querySelector("[data-font=size]");
        this.fontWeightSelect = this.root.querySelector("[data-font=weight]");
        this.fontFamilySelect = this.root.querySelector("[data-font=family]");
        this.textAlignSelect = this.root.querySelector("[data-text=align]");
        this.textLineHeightInpt = this.root.querySelector("[data-text=line_height]");
        this.textColor = this.root.querySelector("[data-text=color]");
        this.init();
    }

    init() {
        let initialFontWeight = parseInt(jQuery(this.fontView).css("font-weight"));
        let initialFontFamily = jQuery(this.fontView).css("font-family");
        let initialTextAlign = jQuery(this.textView).css("text-align");
        let initialFontSize = parseInt(jQuery(this.fontView).css("font-size"));
        let initialLineHeight = parseInt(this.textView.clientHeight);
        let initialColor = jQuery(this.fontView).css("color");
        this.fontSizeInpt.value = initialFontSize;
        this.textLineHeightInpt.value = initialLineHeight;
        this.textColor.value = initialColor;
        jQuery(this.fontWeightSelect).find(`option[value=${initialFontWeight}]`).prop("selected", true);
        jQuery(this.fontFamilySelect).find(`option[value=${initialFontFamily}]`).prop("selected", true);
        jQuery(this.textAlignSelect).find(`option[value=${initialTextAlign}]`).prop("selected", true);

        jQuery(this.positionView).draggable({
            containment: ".parent",
            create:  (event, ui) => {
                this.positionYInpt.value = this.positionView.offsetTop;
                this.positionXInpt.value = this.positionView.offsetLeft;
            },
            drag: (event, ui) => {
                this.positionYInpt.value = ui.position.top;
                this.positionXInpt.value = ui.position.left;
            },
            stop: (event, ui) => {
                ui.position.left = Math.floor(ui.position.left);
                ui.position.top = Math.floor(ui.position.top);
                this.positionYInpt.value = ui.position.top;
                this.positionXInpt.value = ui.position.left;
            },
        });

        jQuery(this.sizeView).resizable({
            alsoResize: this.positionView,
            handles: "e",
            containment: ".a",
            create: (event) => {
                this.widthInpt.value = this.sizeView.clientWidth;
            },
            resize: (event, ui) => {
                this.widthInpt.value = ui.size.width;
            }
        });

        jQuery(this.positionXInpt).on("input", (event) => {
            jQuery(this.positionView).css("left", event.target.value + "px");
        });
        jQuery(this.positionYInpt).on("input", (event) => {
            jQuery(this.positionView).css("top", event.target.value + "px");
        });
        jQuery(this.widthInpt).on("input", (event) => {
            jQuery(this.sizeView).css("width", event.target.value + "px");
        });
        jQuery(this.fontSizeInpt).on("input", event => {
            jQuery(this.fontView).css("font-size", event.target.value + "px");
            this.textLineHeightInpt.value = parseInt(this.textView.clientHeight);
        });

        jQuery(this.fontFamilySelect).on("change", event => {
            jQuery(this.fontView).css("font-family", event.target.value);
        });

        jQuery(this.fontWeightSelect).on("change", event => {
            jQuery(this.fontView).css("font-weight", event.target.value);
        });

        jQuery(this.textAlignSelect).on("change", event =>  {
            jQuery(this.textView).css("text-align", event.target.value);
        });

        jQuery(this.textColor).on("input", event =>  {
            jQuery(this.fontView).css("color", event.target.value);
        });

    }

}

/*Первое поле сделать активным при загрузке*/
jQuery(jQuery(".root")[0]).addClass("active");
jQuery(jQuery(".view")[0]).addClass("active");

Array.from(document.querySelectorAll(".root")).forEach( (root, index) => {
    let viewId = root.dataset["control"];
    let view = document.getElementById(viewId);
    view.addEventListener("mousedown", e => {
        jQuery(".root.active").removeClass("active");
        jQuery(".view.active").removeClass("active");
        jQuery(view).addClass("active");
        jQuery(root).addClass("active");
    });
    new Field({
        view: view,
        root: root
    });
});
let templateOptionsForm = jQuery("#templateOptions");
function onSuccessTemplateOptions(response) {
    "use strict";
    let  data = JSON.parse(response);
    if (data.error !== undefined) {
        jQuery(".alert").text(data.error);
        jQuery(".alert").addClass("alert-danger").removeClass("alert-success");
        jQuery(".alert").show();
        if (data.error_input !== undefined){
            jQuery(data.error_input)[0].setCustomValidity("This field can't be empty");
            let removeValidity = function () {
                jQuery(data.error_input)[0].setCustomValidity("");
                jQuery(data.error_input).off('change input', removeValidity);
            }
            jQuery(data.error_input).on('change input', removeValidity)
        }
    } else if(data.success){
        jQuery(".alert").text(data.success);
        jQuery(".alert").addClass("alert-success").removeClass("alert-danger");
        jQuery(".alert").show();
    } else {
        window.location.href = data.redirectUrl;
    }
}
function saveTemplateOptions(event){
    "use strict";
    event.preventDefault();
    if (templateOptionsForm[0].checkValidity() === false) {
        event.stopPropagation();
    }
    templateOptionsForm.addClass("was-validated");
    jQuery(".alert").hide();
    if (templateOptionsForm[0].checkValidity() === true) {
        jQuery.ajax({
            url: ajaxurl,
            data: templateOptionsForm.serialize(),
            method: "post",
            success: onSuccessTemplateOptions
        });
    }

}

templateOptionsForm.on("submit", saveTemplateOptions);