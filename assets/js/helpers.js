// --- UI Helpers ---
function shakeError(el) {
  if (!el) return;
  el.classList.remove("shake");
  void el.offsetWidth;
  el.classList.add("shake");
}

function setInputState(input, state) {
  if (!input) return;
  input.classList.remove("input-error", "input-ok");
  if (state === "error") input.classList.add("input-error");
  if (state === "ok") input.classList.add("input-ok");
}

function showFieldError(input, message, errorDiv) {
  setInputState(input, "error");
  if (errorDiv) {
    errorDiv.innerText = message || "Please check the form.";
    shakeError(errorDiv);
  }
}

function clearFieldError(input, errorDiv) {
  setInputState(input, "");
  if (errorDiv) errorDiv.innerText = "";
}

// --- Validation helpers ---
function isValidGmail(email) {
  if (!email) return false;
  return /^[^\s@]+@gmail\.com$/i.test(email.trim());
}

function normalizePhone(raw) {
  if (!raw) return "";
  return raw.replace(/\D/g, "");
}

function isValidTRPhoneDigits(digits) {
  if (!digits) return false;
  if (digits.length === 11) return digits.startsWith("0") && digits[1] === "5";
  if (digits.length === 10) return digits.startsWith("5");
  return false;
}

// --- Date formatting ---
function formatDate(iso) {
  try {
    const d = new Date(iso);
    return d.toLocaleString();
  } catch {
    return iso || "-";
  }
}

function getInitialLetter(name) {
  const s = (name || "").trim();
  if (!s) return "?";
  return s[0].toUpperCase();
}

// --- Stock restore (used when cancelling/rejecting orders) ---
function restoreStockForOrder(order) {
  if (!order || !Array.isArray(order.items)) return;

  order.items.forEach((it) => {
    const p = products.find((x) => x.id === it.productId);
    if (p) p.stock += it.quantity;
  });
}


/* =========================
   Location + Distance + Notifications
   ========================= */

// Deterministic small offset so we don't need a real geocoding API.
// Produces a stable coordinate near the city's center (demo-friendly).
function hashStringToUnit(str) {
  let h = 2166136261;
  for (let i = 0; i < str.length; i++) {
    h ^= str.charCodeAt(i);
    h = Math.imul(h, 16777619);
  }
  // 0..1
  return (h >>> 0) / 4294967295;
}

function deriveLatLngFromAddress(city, district, neighborhood) {
  const c = (city || "").toUpperCase();
  const center = (typeof CITY_CENTERS !== "undefined" && CITY_CENTERS[c]) ? CITY_CENTERS[c] : { lat: 39.0, lng: 35.0 };

  const key = `${c}|${district || ""}|${neighborhood || ""}`;
  const u1 = hashStringToUnit(key + "|a");
  const u2 = hashStringToUnit(key + "|b");

  // +/- about 3-4 km jitter (rough): 0.03 degrees lat ~ 3.3 km
  const latOffset = (u1 - 0.5) * 0.06;
  const lngOffset = (u2 - 0.5) * 0.07;

  return { lat: center.lat + latOffset, lng: center.lng + lngOffset };
}

function haversineKm(lat1, lon1, lat2, lon2) {
  const toRad = (d) => d * Math.PI / 180;
  const R = 6371; // km
  const dLat = toRad(lat2 - lat1);
  const dLon = toRad(lon2 - lon1);
  const a = Math.sin(dLat / 2) ** 2 +
            Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
            Math.sin(dLon / 2) ** 2;
  return 2 * R * Math.asin(Math.sqrt(a));
}

function toast(message, type = "info") {
  const wrap = document.getElementById("toastContainer");
  if (!wrap) return;

  const t = document.createElement("div");
  t.className = `toast ${type}`;
  t.textContent = message;

  wrap.appendChild(t);

  setTimeout(() => {
    t.style.opacity = "0";
    t.style.transform = "translateY(6px)";
  }, 2800);

  setTimeout(() => t.remove(), 3400);
}

function getActiveUserObj() {
  if (!activeUser || activeRole !== "user") return null;
  return users?.[activeUser] || null;
}

function addNotification(username, notif) {
  if (!users?.[username]) return;
  if (!Array.isArray(users[username].notifications)) users[username].notifications = [];
  users[username].notifications.unshift(notif);
}

function notifyUsersNearProduct(product) {
  if (!product) return;

  const pLat = Number(product.lat);
  const pLng = Number(product.lng);
  if (!Number.isFinite(pLat) || !Number.isFinite(pLng)) return;

  const now = new Date().toISOString();
  Object.keys(users || {}).forEach((uname) => {
    const u = users[uname];
    const uLat = Number(u?.lat);
    const uLng = Number(u?.lng);
    if (!Number.isFinite(uLat) || !Number.isFinite(uLng)) return;

    const d = haversineKm(uLat, uLng, pLat, pLng);
    if (d <= 5) {
      const notif = {
        id: `${Date.now()}_${Math.random().toString(16).slice(2)}`,
        type: "nearby_product",
        createdAt: now,
        productId: product.id,
        message: `New product near you (${d.toFixed(1)} km): ${product.description} (${product.businessName})`
      };
      addNotification(uname, notif);

      // If the notified user is currently active in this browser, show a toast
      if (activeRole === "user" && activeUser === uname) {
        toast(notif.message, "success");
        if (typeof updateNotifBadge === "function") updateNotifBadge();
        if (typeof renderNotifications === "function") renderNotifications();
      }
    }
  });
}

// Scroll helper for business dashboard topbar
function scrollToBusiness(targetId){
  const el = document.getElementById(targetId);
  if (el) el.scrollIntoView({ behavior: "smooth", block: "start" });
}


/* =========================
   Back to top button
   ========================= */
function scrollToTop() {
  window.scrollTo({ top: 0, behavior: "smooth" });
}

document.addEventListener("DOMContentLoaded", () => {
  const btn = document.getElementById("backToTop");
  if (!btn) return;

  const toggle = () => {
    if (window.scrollY > 60) btn.classList.add("show");
    else btn.classList.remove("show");
  };

  window.addEventListener("scroll", toggle, { passive: true });
  toggle();
});
