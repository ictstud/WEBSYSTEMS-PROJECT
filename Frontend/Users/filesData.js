// Client-side storage for file metadata (not sent to server)
const filesData = JSON.parse(localStorage.getItem("filesData") || "[]");

const form = document.querySelector("#submitFileForm");
const tableBody = document.querySelector("#files-table");

function saveFilesData() {
  localStorage.setItem("filesData", JSON.stringify(filesData));
}

function renderTable() {
  if (!tableBody) return;
  tableBody.innerHTML = "";
  filesData.forEach((item) => {
    const tr = document.createElement("tr");
    tr.innerHTML = `
      <td>${item.ID}</td>
      <td>${escapeHtml(item.last_name)}</td>
      <td>${escapeHtml(item.first_name)}</td>
      <td>${escapeHtml(item.file_name)}</td>
      <td>${escapeHtml(item.date_issued)}</td>
      <td>
        ${
          item.file_data_url
            ? `<button class="see-file-btn" data-id="${item.ID}">See file</button>`
            : `<button disabled>See file</button>`
        }
      </td>
      `;
    tableBody.appendChild(tr);
  });
}

function escapeHtml(s) {
  if (!s && s !== 0) return "";
  return String(s)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

if (form) {
  form.addEventListener("submit", (e) => {
    e.preventDefault();

    const lastName = document.querySelector("#lastName")?.value.trim() || "";
    const firstName = document.querySelector("#firstName")?.value.trim() || "";
    const fileName = document.querySelector("#fileName")?.value.trim() || "";
    const dateIssued =
      document.querySelector("#dateIssued")?.value.trim() || "";
    const fileInput = document.querySelector("#file");

    let fileMeta = { file_type: "", file_size: 0, original_name: "" };
    if (fileInput && fileInput.files && fileInput.files[0]) {
      const f = fileInput.files[0];
      fileMeta.file_type = f.type || "";
      fileMeta.file_size = f.size || 0;
      fileMeta.original_name = f.name || "";
    }

    const id = Date.now();
    const entry = {
      ID: id,
      last_name: lastName,
      first_name: firstName,
      file_name: fileName || fileMeta.original_name,
      date_issued: dateIssued,
      file_type: fileMeta.file_type,
      file_size: fileMeta.file_size,
      // placeholder for data URL; will be set when read completes
      file_data_url: null,
    };
    // If a file was selected, read it as a data URL and then save; otherwise save immediately
    if (fileInput && fileInput.files && fileInput.files[0]) {
      const reader = new FileReader();
      reader.onload = function (ev) {
        entry.file_data_url = ev.target.result; // data URL
        filesData.push(entry);
        saveFilesData();
        renderTable();
        console.log("filesData", filesData);
        form.reset();
      };
      reader.readAsDataURL(fileInput.files[0]);
    } else {
      filesData.push(entry);
      saveFilesData();
      renderTable();
      console.log("filesData", filesData);
      form.reset();
    }
  });
}

// initial render
renderTable();

// Delegate click for See file buttons
if (tableBody) {
  tableBody.addEventListener("click", (e) => {
    const btn = e.target.closest(".see-file-btn");
    if (!btn) return;
    const id = btn.getAttribute("data-id");
    const item = filesData.find((x) => String(x.ID) === String(id));
    if (!item || !item.file_data_url) {
      alert("File not available");
      return;
    }

    // Open data URL in a new tab/window
    const w = window.open();
    // If opening blank failed (popup blocker), fallback to setting location
    if (!w) {
      window.location.href = item.file_data_url;
      return;
    }
    // Create minimal HTML to show the file (img/pdf/text will render)
    w.document.write(`<title>${escapeHtml(item.file_name)}</title>`);
    w.document.write(`<body style="margin:0">`);
    // If it's an image, embed with full size; otherwise embed in an iframe
    if (item.file_type && item.file_type.startsWith("image/")) {
      w.document.write(
        `<img src="${item.file_data_url}" alt="${escapeHtml(
          item.file_name
        )}" style="width:100%;height:auto;display:block">`
      );
    } else {
      w.document.write(
        `<iframe src="${item.file_data_url}" style="border:0;width:100%;height:100vh"></iframe>`
      );
    }
    w.document.write("</body>");
    w.document.close();
  });
}
