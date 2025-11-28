// ============================================
//  GLOBAL API BASE PATH
// ============================================
const apiBase = "/api"; // Adjust if your server path differs

// ============================================
//  Toast Notification Helper
// ============================================
function showToast(text, type = "info") {
  const container = document.getElementById("toastContainer");
  if (!container) return;

  const id = "t" + Date.now();

  container.insertAdjacentHTML("beforeend", `
        <div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2">
            <div class="d-flex">
                <div class="toast-body">${text}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `);

  const el = document.getElementById(id);
  const t = new bootstrap.Toast(el, { delay: 4000 });
  t.show();
}

// ============================================
//  CREATE ORDER (index.html)
// ============================================
const paymentForm = document.getElementById("paymentForm");

if (paymentForm) {
  paymentForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const btnText = document.getElementById("btnText");
    const btnLoader = document.getElementById("btnLoader");

    btnText.classList.add("d-none");
    btnLoader.classList.remove("d-none");

    // Build body EXACTLY like Postman
    const payload = {
      payment_name: "cashpay",
      currency_id: document.getElementById("currency").value,
      payerPhone: document.getElementById("payerPhone").value.trim(),
      payerEmail: document.getElementById("payerEmail").value.trim(),
      beneficiaryList: [
        {
          amount: Number(document.getElementById("amount").value),
          itemName: "item",
          quantity: 1
        }
      ],
      des: document.getElementById("description").value.trim()
    };

    try {
      const res = await fetch(apiBase + "/createOrder.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      document.getElementById("resultBox").classList.remove("d-none");
      document.getElementById("resultPre").textContent =
          JSON.stringify(data, null, 2);

      if (data.status && data.order_id) {
        showToast("تم إنشاء الطلب بنجاح", "success");

        // Save valid data for checkorder
        localStorage.setItem("onepay_last_order", data.order_id);
        localStorage.setItem("onepay_last_payer", payload.payerPhone);
        localStorage.setItem("onepay_last_email", payload.payerEmail || "");

        // Save requestIdRes (OnePay returns different formats)
        if (data.raw && data.raw.requestIdRes)
          localStorage.setItem("onepay_last_requestId", data.raw.requestIdRes);

        if (data.raw?.data?.requestIdRes)
          localStorage.setItem("onepay_last_requestId", data.raw.data.requestIdRes);

      } else {
        showToast(data.error || "خطأ أثناء إنشاء الطلب", "danger");
      }

    } catch (err) {
      showToast("خطأ في الاتصال بالسيرفر", "danger");

    } finally {
      btnText.classList.remove("d-none");
      btnLoader.classList.add("d-none");
    }
  });
}

// ============================================
//  CHECK ORDER (status.html)
// ============================================
const statusForm = document.getElementById("statusForm");

if (statusForm) {
  statusForm.addEventListener("submit", async (e) => {
    e.preventDefault();

    const loader = document.getElementById("statusLoader");
    loader.classList.remove("d-none");

    const order_id = document.getElementById("statusOrderId").value.trim();

    // Build exact JSON required by Postman
    const payload = {
      payment_name: "cashpay",
      payerPhone: localStorage.getItem("onepay_last_payer") || "",
      payerEmail: localStorage.getItem("onepay_last_email") || "",
      requestIdRes: localStorage.getItem("onepay_last_requestId") || "",
      orderID: order_id
    };

    try {
      const res = await fetch(apiBase + "/checkOrder.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      document.getElementById("statusResult").classList.remove("d-none");
      document.getElementById("statusPre").textContent =
          JSON.stringify(data, null, 2);

      showToast("تم جلب حالة الطلب", "info");

    } catch (err) {
      showToast("خطأ في الاتصال بالسيرفر", "danger");

    } finally {
      loader.classList.add("d-none");
    }
  });
}
