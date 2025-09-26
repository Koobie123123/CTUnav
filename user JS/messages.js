function toggleDropdown() {
  document.getElementById("dropdownMenu").style.display =
    document.getElementById("dropdownMenu").style.display === "block" ? "none" : "block";
}
window.onclick = function(event) {
  const menu = document.getElementById("profileMenu");
  if (!menu.contains(event.target)) {
    document.getElementById("dropdownMenu").style.display = "none";
  }
};

// ✅ Open chat
let currentStaffID = null;
function openChat(staffID, staffName, staffDept) {
  currentStaffID = staffID;
  document.getElementById("receiverID").value = staffID;

  // only change text inside chatWith
  document.getElementById("chatWith").innerHTML = `
    <strong>${staffName}</strong><br>
    <small style="font-size: 12px; color: #666;">${staffDept}</small>
  `;

  // 🔹 Mark messages as read before loading
  markMessagesAsRead(staffID);

  loadMessages();
}

function markMessagesAsRead(staffID) {
  fetch("user_mark_as_read.php", {
    method: "POST",
    headers: { "Content-Type": "application/x-www-form-urlencoded" },
    body: "staffID=" + staffID
  })
  .then(response => response.text())
  .then(data => {
    console.log("Marked as read:", data);
  })
  .catch(err => console.error("Error marking messages:", err));
}


// Toggle files dropdown
// Toggle files dropdown
function toggleFilesDropdown() {
  const dropdown = document.getElementById("filesDropdown");
  dropdown.classList.toggle("hidden");

  if (!dropdown.classList.contains("hidden")) {
    loadConversationFiles(); // fetch files when opened
  }
}

// Load files for the current conversation
function loadConversationFiles() {
  const receiverID = document.getElementById("receiverID").value;
  if (!receiverID) return;

  fetch(`messages.php?action=files&receiverID=${receiverID}`)
    .then(res => res.json())
    .then(files => {
      const grid = document.getElementById("filesGrid");
      const noFilesMsg = document.getElementById("noFilesMsg");
      grid.innerHTML = "";

      if (files.length === 0) {
        noFilesMsg.style.display = "block";
      } else {
        noFilesMsg.style.display = "none";
        files.forEach(file => {
          const ext = file.file_path.split('.').pop().toLowerCase();

          let fileHTML = "";
          if (["jpg", "jpeg", "png", "gif"].includes(ext)) {
            fileHTML = `<img src="${file.file_path}" alt="file" style="max-width:100px; border-radius:6px;">`;
          } else {
            fileHTML = `<a href="${file.file_path}" target="_blank">📎 ${file.file_name}</a>`;
          }

          grid.innerHTML += `
            <div class="file-item" style="margin:5px; text-align:center;">
              ${fileHTML}
            </div>
          `;
        });
      }
    })
    .catch(err => {
      console.error("Error loading files:", err);
    });
}


// ✅ Load messages
function loadMessages() {
  if (!currentStaffID) return;
  fetch("messages.php?action=load&receiverID=" + currentStaffID)
    .then(res => res.text())
    .then(data => {
      document.getElementById("chatBox").innerHTML = data;
      document.getElementById("chatBox").scrollTop = document.getElementById("chatBox").scrollHeight;
    });
}

// ✅ Send message
function sendMessage(e) {
  e.preventDefault();
  const msg = document.getElementById("chatInput").value;
  const receiverID = document.getElementById("receiverID").value;
  const files = document.getElementById("fileInput").files;

  if (!receiverID || (msg.trim() === "" && files.length === 0)) return;

  let formData = new FormData();
  formData.append("receiverID", receiverID);
  formData.append("message", msg);

  for (let i = 0; i < files.length; i++) {
    formData.append("files[]", files[i]);
  }

  fetch("messages.php", {
    method: "POST",
    body: formData
  }).then(res => res.text())
    .then(resp => {
      document.getElementById("chatInput").value = "";
      document.getElementById("fileInput").value = "";
      document.getElementById("filePreview").innerHTML = "";
      selectedFiles = [];
      loadMessages();
    });
}

let selectedFiles = [];

document.getElementById("fileInput").addEventListener("change", function() {
  const previewContainer = document.getElementById("filePreview");
  previewContainer.innerHTML = ""; // clear previous previews
  selectedFiles = Array.from(this.files); // store selected files

  selectedFiles.forEach((file, index) => {
    const fileDiv = document.createElement("div");
    fileDiv.style.position = "relative";
    fileDiv.style.display = "inline-block";

    // If image → show thumbnail, else → show filename
    if (file.type.startsWith("image/")) {
      const img = document.createElement("img");
      img.src = URL.createObjectURL(file);
      img.style.maxWidth = "80px";
      img.style.maxHeight = "80px";
      img.style.borderRadius = "6px";
      img.style.objectFit = "cover";
      fileDiv.appendChild(img);
    } else {
      const p = document.createElement("div");
      p.textContent = "📎 " + file.name;
      p.style.padding = "4px 6px";
      p.style.border = "1px solid #ccc";
      p.style.borderRadius = "4px";
      p.style.background = "#f8f8f8";
      fileDiv.appendChild(p);
    }

    // ❌ Remove button
    const removeBtn = document.createElement("span");
    removeBtn.innerHTML = "&times;";
    removeBtn.style.position = "absolute";
    removeBtn.style.top = "0px";
    removeBtn.style.right = "4px";
    removeBtn.style.cursor = "pointer";
    removeBtn.style.color = "red";
    removeBtn.style.fontWeight = "bold";
    removeBtn.onclick = () => {
      selectedFiles.splice(index, 1); // remove file
      const dt = new DataTransfer();
      selectedFiles.forEach(f => dt.items.add(f));
      document.getElementById("fileInput").files = dt.files;
      fileDiv.remove();
    };
    fileDiv.appendChild(removeBtn);

    previewContainer.appendChild(fileDiv);
  });
});
