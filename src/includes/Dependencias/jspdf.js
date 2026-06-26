document.addEventListener("DOMContentLoaded", function () {
    const btnPDF = document.getElementById("btnExportarPDF");

    if (btnPDF) {
        btnPDF.addEventListener("click", generarPDF);
    }
});

function generarPDF() {
    if (!window.jspdf) {
        alert("Error: jsPDF no está cargado.");
        return;
    }

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF("p", "mm", "a4");

    agregarEncabezado(doc);
    const finalY = agregarTablaResumen(doc);
    agregarPie(doc, finalY);
    guardarPDF(doc);
}

function agregarEncabezado(doc) {
    doc.setFont("helvetica", "bold");
    doc.setFontSize(16);
    doc.text("ASISTENCIAS LABORALES", 105, 20, { align: "center" });

    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);
    doc.text(`Empleado: ${window.datosEmpleado.nombre}`, 20, 40);
    doc.text(`Rol: ${window.datosEmpleado.rol}`, 20, 50);
    doc.text(`Estado: ${window.datosEmpleado.estado}`, 20, 60);
}

function agregarTablaResumen(doc) {
    if (typeof doc.autoTable !== "function") {
        alert("Error: autoTable no está disponible. Verifique la librería jspdf-autotable.");
        return 80;
    }

    doc.autoTable({
        startY: 80,
        head: [["Periodo", "Rango", "Horas"]],
        body: [
            ["Semana", window.datosEmpleado.rangoSemana, window.datosEmpleado.horasSemana],
            ["Quincena", window.datosEmpleado.rangoQuincena, window.datosEmpleado.horasQuincena],
            ["Mes", window.datosEmpleado.rangoMes, window.datosEmpleado.horasMes],
            ["Año", window.datosEmpleado.rangoAnio, window.datosEmpleado.horasAnio],
            ["Total Histórico", "-", window.datosEmpleado.totalHoras]
        ],
        styles: { fontSize: 10 },
        headStyles: { fillColor: [41, 128, 185] }
    });

    return doc.lastAutoTable.finalY;
}

function agregarPie(doc, y) {
    doc.setFontSize(10);
    doc.text(`Fecha de generación: ${window.datosEmpleado.fechaActual}`, 190, y + 20, { align: "right" });
}

function guardarPDF(doc) {
    const nombreSeguro = window.datosEmpleado.nombre
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .replace(/[^a-zA-Z0-9]/g, "_")
        .toLowerCase();

    const fechaArchivo = window.datosEmpleado.fechaActual.replace(/[: ]/g, "_");

    doc.save(`asistencias_${nombreSeguro}_${fechaArchivo}.pdf`);
}
