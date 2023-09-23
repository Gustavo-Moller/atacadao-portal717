function Requester (target)  {
    const config = {
        method: "GET",
        headers: {},
        body: null
    }

    function setMethod(method) {
        config.method = method.toUpperCase()
    }

    function setHeaders(headers) {
        config.headers = headers
    }

    function setBody(body) {
        config.body = body
    }

    function send() {
        return fetch(target, config)
    }

    return {
        setMethod,
        setHeaders,
        setBody,
        send
    }
}

function doTableActions(tableActions, myTableInstance) {
    const fullscreenModal = FullscreenModal()
    const createButton = tableActions.querySelector(".create-button")
    const updateButton = tableActions.querySelector(".update-button")
    const expandButton = tableActions.querySelector(".expand-button")
    const { tableToUpdate } = tableActions.dataset
    const tableCard = document.querySelector(`#table-card-${tableToUpdate}`)

    function createButtonAction() {
        createButton.addEventListener("click",() => {
            const html = `
                <div>
                
                </div>
            `;
            expandTable()
        })
    }

    function updateButtonAction() {
        const target = `${baseUrl}/crudx/table/${tableToUpdate}`
        const requester = Requester(target)

        updateButton.addEventListener("click", () => {
            myTableInstance.startLoading()
            requester.send()
                .then(response => response.json())
                .then(json => {
                    myTableInstance.loadNewRows(json.rows.map(Object.values))
                    myTableInstance.endLoading()
                })
        })
    }

    function expandTable() {
        const myTable = tableCard.querySelector(`.my-table`).cloneNode(true)
        fullscreenModal.clearBody()
        fullscreenModal.append(myTable)

        fullscreenModal.show()
    }



    function expandButtonAction() {
        expandButton.addEventListener("click", expandTable)
    }

    function startActions() {
        createButtonAction()
        updateButtonAction()
        expandButtonAction()
    }

    startActions()
}

function FullscreenModal() {
    const nodeFullscreenModal = document.querySelector("#modal-expanded-table")
    const bootstrapFullscreenModal = bootstrap.Modal.getOrCreateInstance(nodeFullscreenModal)
    const body = nodeFullscreenModal.querySelector(".modal-body")

    function getNode() {
        return nodeFullscreenModal
    }

    function getBoostrapInstance() {
        return bootstrapFullscreenModal
    }

    function clearBody() {
        body.querySelector(".main").innerHTML = ""
    }

    function append(node) {
        body.querySelector(".main").append(node)
    }

    function show() {
        bootstrapFullscreenModal.show()
    }

    function hide() {
        bootstrapFullscreenModal.hide()
    }

    return {
        getBoostrapInstance,
        getNode,
        append,
        clearBody,
        show,
        hide
    }
}



document.addEventListener("DOMContentLoaded", () => {
    const fullscreenModal = FullscreenModal()
    const nodeFullscreenModal = fullscreenModal.getNode()

    nodeFullscreenModal.querySelectorAll("button.close").forEach(button => {
        button.addEventListener("click", () => {
            fullscreenModal.hide()
        })
    })

    document.querySelectorAll(".update-table").forEach(tableWrapper => {
        const myTable = MyTable(tableWrapper.querySelector(".my-table"))
        const tableActions = tableWrapper.querySelector(".table-actions")

        doTableActions(tableActions, myTable)
    })
})