import axios from "axios";
import Chart from "chart.js/auto"; // Untuk Chart.js v3+
import {
    Html5Qrcode,
    Html5QrcodeScanType,
    Html5QrcodeScannerState,
} from "html5-qrcode"; // Impor juga Html5QrcodeScanType

window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
window.Chart = Chart;
// resources/js/app.js atau resources/js/bootstrap.js
window.Html5Qrcode = Html5Qrcode;
window.Html5QrcodeScanType = Html5QrcodeScanType; // <-- Jadikan global
window.Html5QrcodeScannerState = Html5QrcodeScannerState; // Tambahkan ini
