function salinKeTabelExcel() {
  const webTable = document.querySelector("#tabel-web");
  const excelTable = document.querySelector("#tabel-excel");
  excelTable.innerHTML = "";

  const thead = document.createElement("thead");
  const trHead = document.createElement("tr");

  webTable.querySelectorAll("thead th").forEach((th, i) => {
    const thNew = document.createElement("th");
    thNew.textContent = th.textContent;
    trHead.appendChild(thNew);
  });
  thead.appendChild(trHead);
  excelTable.appendChild(thead);

  const tbody = document.createElement("tbody");

  webTable.querySelectorAll("tbody tr").forEach((row) => {
    const newRow = document.createElement("tr");

    row.querySelectorAll("td").forEach((cell, index) => {
      const newCell = document.createElement("td");

      // Kolom bukti
      if (index === 6) {
        const link = cell.querySelector("a");
        if (link) {
          const fileName = link.href.split("/").pop();
          newCell.textContent = fileName;
        } else {
          newCell.textContent = "-";
        }
      } else {
        newCell.textContent = cell.textContent.trim();
      }

      newRow.appendChild(newCell);
    });

    tbody.appendChild(newRow);
  });

  excelTable.appendChild(tbody);
}

document.addEventListener("DOMContentLoaded", function () {
  const btn = document.querySelector("#exportExcel");
  if (btn) {
    btn.addEventListener("click", function () {
      salinKeTabelExcel();
      $("#tabel-excel").table2excel({
        name: "Rekap Absensi",
        filename: "rekap_absensi_" + new Date().toLocaleDateString("id-ID"),
        preserveColors: false,
      });
    });
  }
});
