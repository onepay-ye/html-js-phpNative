// ملف مشترك لكل الصفحات
const apiBase = "/api"; // تأكد أن مسار الـ API صحيح حسب روت السيرفر

// عرض توست
function showToast(text, type = "info") {
  const container = document.getElementById("toastContainer");
  if(!container) return;
  const id = "t" + Date.now();
  container.insertAdjacentHTML("beforeend", `
    <div id="${id}" class="toast align-items-center text-bg-${type} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
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

// --- صفحة index.html: إنشاء طلب دفع
const paymentForm = document.getElementById("paymentForm");
if(paymentForm){
  paymentForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const submitBtn = document.getElementById("submitBtn");
    const btnText = document.getElementById("btnText");
    const btnLoader = document.getElementById("btnLoader");
    btnText.classList.add("d-none");
    btnLoader.classList.remove("d-none");

    const payload = {
      payerPhone: document.getElementById("payerPhone").value.trim(),
      payerEmail: document.getElementById("payerEmail").value.trim(),
      currency: document.getElementById("currency").value,
      amount: document.getElementById("amount").value,
      description: document.getElementById("description").value.trim()
    };

    try{
      const res = await fetch(apiBase + "/createOrder.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify(payload)
      });
      const data = await res.json();

      document.getElementById("resultBox").classList.remove("d-none");
      document.getElementById("resultPre").textContent = JSON.stringify(data, null, 2);

      if(data.status && data.order_id){
        showToast("تم إنشاء الطلب بنجاح", "success");
        // حفظ order_id محلياً لاستخدامه في OTP أو الفحص
        localStorage.setItem("onepay_last_order", data.order_id);
      } else {
        showToast(data.error || "خطأ أثناء إنشاء الطلب", "danger");
      }

    } catch(err){
      showToast("خطأ في الاتصال بالسيرفر", "danger");
    } finally {
      btnText.classList.remove("d-none");
      btnLoader.classList.add("d-none");
    }
  });
}

// --- صفحة otp.html
const otpForm = document.getElementById("otpForm");
if(otpForm){
  otpForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const btn = document.getElementById("otpBtn");
    const loader = document.getElementById("otpLoader");
    loader.classList.remove("d-none");

    const payload = {
      order_id: document.getElementById("orderId").value.trim(),
      otp: document.getElementById("otpCode").value.trim()
    };

    try {
      const res = await fetch(apiBase + "/confirmOtp.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      document.getElementById("otpResult").classList.remove("d-none");
      document.getElementById("otpPre").textContent = JSON.stringify(data, null, 2);
      showToast(data.status === 1 ? "تم تأكيد الدفع" : (data.error || "فشل التأكيد"), data.status === 1 ? "success" : "danger");
    } catch (err){
      showToast("خطأ في الاتصال بالسيرفر", "danger");
    } finally {
      loader.classList.add("d-none");
    }
  });
}

// --- صفحة status.html
const statusForm = document.getElementById("statusForm");
if(statusForm){
  statusForm.addEventListener("submit", async (e) => {
    e.preventDefault();
    const loader = document.getElementById("statusLoader");
    loader.classList.remove("d-none");

    const order_id = document.getElementById("statusOrderId").value.trim();
    try {
      const res = await fetch(apiBase + "/checkOrder.php?order_id=" + encodeURIComponent(order_id));
      const data = await res.json();
      document.getElementById("statusResult").classList.remove("d-none");
      document.getElementById("statusPre").textContent = JSON.stringify(data, null, 2);
      showToast("تم جلب حالة الطلب", "info");
    } catch (err){
      showToast("خطأ في الاتصال بالسيرفر", "danger");
    } finally {
      loader.classList.add("d-none");
    }
  });
}
