/****************************************************
 * LOADING BUTTON HANDLER
 ****************************************************/
function startLoading() {
    sendBtn.setAttribute("disabled", true);
    document.getElementById("btnText").classList.add("d-none");
    document.getElementById("btnLoader").classList.remove("d-none");
}

function stopLoading() {
    document.getElementById("btnText").classList.remove("d-none");
    document.getElementById("btnLoader").classList.add("d-none");
    updateButtonState(); // يعيد تمكين الزر عند اكتمال الشروط
}


/****************************************************
 * AUTO-DETECT API PATH
 ****************************************************/
function autoDetectAPI() {
    let current = window.location.pathname;

    if (current.includes("public/")) {
        current = current.replace(/public\/.*/, "api");
    } else {
        current = current.replace(/\/[^\/]*$/, "/api");
    }

    return window.location.origin + current;
}

const baseAPI = autoDetectAPI();
console.log("Detected API:", baseAPI);


/****************************************************
 * ELEMENTS
 ****************************************************/
const apiSelect = document.getElementById("apiSelect");
const walletSelect = document.getElementById("walletSelect");
const jsonInput = document.getElementById("jsonInput");
const emailBox = document.getElementById("emailBox");
const invoiceEmail = document.getElementById("invoiceEmail");
const responseBox = document.getElementById("responseBox");
const sendBtn = document.getElementById("sendBtn");


/****************************************************
 * JSON Templates
 ****************************************************/
function getTemplate(operation, wallet) {

    switch (operation) {

        case "createorder":
            return {
                payment_name: wallet,
                currency_id: "YER",
                payerPhone: "777000000",
                payerEmail: "example@mail.com",
                beneficiaryList: [
                    {
                        amount: 100,
                        itemName: "item",
                        quantity: 1
                    }
                ],
                des: "Test payment"
            };

        case "checkorder":
            return {
                payment_name: wallet,
                payerPhone: "777000000",
                payerEmail: "example@mail.com",
                requestIdRes: "",
                orderID: ""
            };

        case "accountinfo":
            return {
                payment_name: wallet
            };

        default:
            return {};
    }
}


/****************************************************
 * Enable/Disable Send Button Automatically
 ****************************************************/
function updateButtonState() {
    const operation = apiSelect.value;
    const wallet = walletSelect.value;

    // For GET invoice list: only email is needed
    if (operation === "invoicelist") {
        if (invoiceEmail.value.trim() !== "") {
            sendBtn.removeAttribute("disabled");
        } else {
            sendBtn.setAttribute("disabled", true);
        }
        return;
    }

    // For POST requests: JSON must exist
    if (operation && wallet && jsonInput.value.trim() !== "") {
        sendBtn.removeAttribute("disabled");
    } else {
        sendBtn.setAttribute("disabled", true);
    }
}


/****************************************************
 * Operation Change Handler
 ****************************************************/
apiSelect.addEventListener("change", () => {

    const operation = apiSelect.value;
    const wallet = walletSelect.value;

    if (operation === "invoicelist") {
        emailBox.classList.remove("d-none");
        jsonInput.value = ""; // GET → no JSON body
    } else {
        emailBox.classList.add("d-none");
        jsonInput.value = JSON.stringify(getTemplate(operation, wallet), null, 2);
    }

    updateButtonState();
});


/****************************************************
 * Wallet Change Handler
 ****************************************************/
walletSelect.addEventListener("change", () => {

    const operation = apiSelect.value;
    const newWallet = walletSelect.value;

    if (operation !== "invoicelist") {
        jsonInput.value = JSON.stringify(getTemplate(operation, newWallet), null, 2);
    }

    updateButtonState();
});


/****************************************************
 * JSON Input Change
 ****************************************************/
jsonInput.addEventListener("input", updateButtonState);
invoiceEmail.addEventListener("input", updateButtonState);



/****************************************************
 * SEND REQUEST HANDLER
 ****************************************************/
sendBtn.addEventListener("click", async () => {

    startLoading();

    const operation = apiSelect.value;
    const wallet = walletSelect.value;
    let url = "";
    let method = "POST";
    let payload = {};

    try {
        /************** ROUTING **************/
        if (operation === "accountinfo") {
            url = `${baseAPI}/accountInfo.php`;
            method = "POST";
        }

        else if (operation === "invoicelist") {
            if (!invoiceEmail.value.trim()) {
                responseBox.textContent = "❌ Please enter email.";
                stopLoading();
                return;
            }

            url = `${baseAPI}/invoiceList.php?email=${encodeURIComponent(invoiceEmail.value)}&wallet=${wallet}`;
            method = "GET";
        }

        else if (operation === "createorder") {
            url = `${baseAPI}/createOrder.php`;
        }

        else if (operation === "checkorder") {
            url = `${baseAPI}/checkOrder.php`;
        }


        /************** POST Requests **************/
        if (method === "POST") {
            try {
                payload = JSON.parse(jsonInput.value || "{}");
            } catch (e) {
                responseBox.textContent = "❌ Invalid JSON";
                stopLoading();
                return;
            }

            payload.payment_name = wallet;
        }


        /************** SEND REQUEST **************/
        const res = await fetch(url, {
            method,
            headers: { "Content-Type": "application/json" },
            body: method === "POST" ? JSON.stringify(payload) : null
        });

        const data = await res.json();
        responseBox.textContent = JSON.stringify(data, null, 2);

    } catch (err) {
        responseBox.textContent = "❌ ERROR: " + err;

    } finally {
        stopLoading();  // Always stop loader even on error
    }
});
