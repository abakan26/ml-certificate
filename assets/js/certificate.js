window.addEventListener("load", function () {
    // let filterForm = document.getElementById("filterForm");
    let table = new CertificateTable({
        form: document.getElementById("filterForm"),
        table: document.getElementById("dataTable"),
        pagination: document.getElementById("dataTable_paginate"),
        result_count: document.getElementById("dataTable_info"),
    });
});

class CertificateTable {
    constructor(props) {
        this.form = props.form;
        this.table = props.table;
        this.body = this.table.querySelector("tbody");
        this.pagination = props.pagination;
        this.orders = props.table.querySelectorAll("[data-order-by]");
        this.result_count = props.result_count;
        this.applyFiltersBtn = this.form.querySelector("[data-action=apply_filters]");
        this.resetFiltersBtn = this.form.querySelector("[data-action=reset_filters]");
        this.deleteBtn = this.form.querySelector("[data-action=delete]");
        this.setNewTemplateBtn = this.form.querySelector("[data-action=set_new_template]");
        this.searchBtn = this.form.querySelector("[data-action=search_by_email]");
        this.perPageControl = this.form.querySelector(`[aria-controls=${this.table.id}]`);
        this.state = {};
        this.onCreate();
    }

    render(html) {
        this.body.innerHTML = "";
        this.pagination.innerHTML = "";
        this.result_count.innerHTML = "";
        this.body.innerHTML = html.tbody;
        this.result_count.innerHTML = html.result_count;
        this.pagination.innerHTML = html.pagination;
        this.onRender();
    }

    getState() {
        return this.state = formToObject(this.form);
    }

    onCreate() {
        this.state = this.getState();
        this.applyFiltersBtn.addEventListener("click", event => this.applyFilters())
        this.resetFiltersBtn.addEventListener("click", event => this.resetFilters())
        this.deleteBtn.addEventListener("click", event => this.delete());
        this.perPageControl.addEventListener("change", event => this.perPageHandler(event));
        this.setNewTemplateBtn.addEventListener("click", event => this.setNewTemplate(event));
        this.searchBtn.addEventListener("click", event => this.searchByEmail(event));
        this.form.elements["search_by_email"].addEventListener("keydown", event => {
            if (event.code === 'Enter') {
                event.preventDefault();
                this.searchByEmail(event);
                return false;
            }
        })
        Array.from(this.orders).forEach(
            order => order.addEventListener("click", () => this.orderHandler(order))
        );
        this.onRender();
    }

    onRender() {
        let item = this.pagination.querySelectorAll("a.page-link")
        Array.from(item).forEach(item => item.addEventListener('click', event => this.setPagination(event, item)))
    }

    setPagination(event, link) {
        event.preventDefault();
        this.setPage(link.dataset.pageNum)
        this.submit();
    }

    setViewState(state) {
        let container = this.table.closest(".card");
        switch (state) {
            case "loading":
                container.classList.add("table-loading");
                break;
            default:
                container.classList.remove("table-loading")
        }
    }

    applyFilters() {
        this.form.elements["search_by_email"].value = "";
        this.setPage(1);
        this.submit();
    }

    perPageHandler(event) {
        this.setPerPage(event.target.value);
        this.setPage(1);
        this.submit();
    }

    resetFilters() {
        Array.from(this.form.querySelectorAll("[name^=filter]")).forEach(control => control.value = "");
        this.setPage(1);
        this.submit();
    }

    searchByEmail(event) {
        let email = this.form.elements["search_by_email"].value;
        if (email === ""){
            alert("Введите email");
            return false;
        }
        this.resetFilters();
    }

    delete() {
        let certificates = this.getState().certificates;
        if (certificates === undefined) return alert("Не выбран ни один сертификат");
        if (confirm("Вы уверены, что хотите удалить " + Object.keys(certificates).length + " элемента")) {
            this.setViewState("loading");
            this.doActionFromServer({
                action: "ml_delete_certificate",
                data: JSON.stringify(certificates),
                success: json => {
                    if (json.status === "success") {
                        Object.values(certificates).forEach(id => this.removeFromTable(id));
                    }
                },
                finally: () => this.setViewState()
            })
        }

    }

    setNewTemplate(event) {
        let certificates = this.getState().certificates;
        let newTemplateID = this.form.elements["new_template"].value;
        if (newTemplateID === "") return alert("Не выбран шаблон");
        if (certificates === undefined) return alert("Не выбран ни один сертификат");
        this.setViewState("loading");
        this.doActionFromServer({
            action: "ml_update_certificates_template_id",
            data: JSON.stringify({
                certificates: certificates,
                template_id: newTemplateID
            }),
            success: json => {
                if (json.status === "success") {
                    alert("Изменения сохранены");
                    this.setPage(1);
                    this.submit();
                }
            },
            finally: () => this.setViewState()
        })
    }

    removeFromTable(id) {
        let tr = document.getElementById("certificate_tr_" + id);
        tr.parentNode.removeChild(tr);
        setTimeout(() => {
            this.setPage(1);
            this.submit();
        }, 1000);
    }

    setPage(value) {
        this.form.elements['page_num'].value = value;
    }

    setPerPage(value) {
        this.form.elements['per_page'].value = value;
    }

    orderHandler(tdOrder) {
        let orderBy = tdOrder.dataset.orderBy;
        let order = "asc";
        if (tdOrder.classList.contains("sorted")){
            order = tdOrder.classList.contains("asc") ? "desc" : "asc";
        }
        Array.from(this.orders).forEach(function (td) {
            td.classList.remove("sorted");
            td.classList.remove("desc");
            td.classList.remove("asc");
            td.classList.add("sortable");
            td.classList.add("asc");
        });
        this.setOrder(orderBy, order)
        this.setPage(1);
        this.submit();
    }

    setOrder(orderBy, order) {
        let tdOrder = this.table.querySelector("[data-order-by=" + orderBy + "]");
        tdOrder.classList.remove("sortable");
        tdOrder.classList.remove("asc");
        tdOrder.classList.add("sorted");
        tdOrder.classList.add(order);

        this.form.elements["orderby"].value = orderBy;
        this.form.elements["order"].value = order;

    }

    doActionFromServer(props) {
        let formData = new FormData();
        formData.append("action", props.action);
        formData.append("data", props.data);

        fetch(ajaxurl + `?action=${props.action}`, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(json => props.success(json))
            .catch(error => console.log(error))
            .finally(() => props.finally())
    }

    submit() {
        this.setViewState("loading");
        this.doActionFromServer({
            action: "ml_certificate_filtered",
            data: JSON.stringify(this.getState()),
            success: data => {
                this.render(data.html);
            },
            finally: () => this.setViewState()
        })
    }
}
