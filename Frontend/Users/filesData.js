// Upload file to backend and reload page (preserves server-rendered DOM)
// Attach handlers to existing "OPEN FILE" buttons without modifying DOM

document.addEventListener("DOMContentLoaded", function () {
  // Intercept form submit to upload file to backend
  const form = document.getElementById("submitFileForm");
  if (form) {
    form.addEventListener("submit", async function (e) {
      const fileInput = form.querySelector("input[type=file]");

      // If no file selected, show error and prevent submit
      if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
        e.preventDefault();
        alert("Please select a file to submit");
        return;
      }

      // File is selected - upload via AJAX
      e.preventDefault();

      const file = fileInput.files[0];
      const formData = new FormData();
      formData.append("file", file);

      // Add form fields
      ["last_name", "first_name", "file_name", "date_issued"].forEach(
        (name) => {
          const el = form.querySelector('[name="' + name + '"]');
          if (el) formData.append(name, el.value);
        }
      );

      try {
        const resp = await fetch("../Backend/file_api.php?action=upload", {
          method: "POST",
          body: formData,
        });

        // Check if response is ok
        if (!resp.ok) {
          const text = await resp.text();
          alert("Upload failed with status " + resp.status + ": " + text);
          return;
        }

        // Parse JSON response
        let data;
        try {
          data = await resp.json();
        } catch (parseErr) {
          const text = await resp.text();
          alert("Invalid response from server: " + text);
          return;
        }

        if (data && data.ok) {
          // Reload to show new row from server
          window.location.reload();
        } else {
          alert(
            "Upload failed: " +
              (data && data.error ? data.error : "unknown error")
          );
        }
      } catch (err) {
        alert("Upload error: " + err.message);
      }
    });
  }

  // Attach click handlers to existing "OPEN FILE" buttons (non-destructive)
  // Find the table ID from first row's first cell to map to file ID
  const seeFileButtons = document.querySelectorAll("button.see-file-btn");
  seeFileButtons.forEach((btn) => {
    const tr = btn.closest("tr");
    if (!tr) return;

    const idCell = tr.querySelector("td");
    if (!idCell) return;

    const fileId = idCell.textContent.trim();

    // Check if server has file blob for this row
    fetch("../Backend/file_api.php?action=has&id=" + encodeURIComponent(fileId))
      .then((r) => {
        if (!r.ok) throw new Error("Not found");
        return r.text();
      })
      .then((text) => {
        try {
          const j = JSON.parse(text);
          if (j && j.ok && j.has) {
            btn.disabled = false;
            btn.addEventListener("click", function (e) {
              e.preventDefault();
              const url =
                "../Backend/file_api.php?action=download&id=" +
                encodeURIComponent(fileId);
              window.open(url, "_blank");
            });
          } else {
            btn.disabled = true;
          }
        } catch (e) {
          btn.disabled = true;
        }
      })
      .catch(() => {
        btn.disabled = true;
      });
  });
});
