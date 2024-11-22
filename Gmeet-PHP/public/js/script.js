document.getElementById('diagnostic_lab').addEventListener('change', updateHospitalOptions);
document.getElementById('hospital').addEventListener('change', updateDoctorOptions);

async function updateHospitalOptions() {
  const diagnosticLab = document.getElementById('diagnostic_lab').value;
  const response = await fetch(`/getHospitals`);
  const hospitals = await response.json();

  let options = '';
  hospitals.forEach(hospital => {
    if (diagnosticLab === 'teleconsult' && hospital.type === 'teleconsult') {
      options += `<option value="${hospital.id}">${hospital.name}</option>`;
    } else if (diagnosticLab !== 'teleconsult') {
      options += `<option value="${hospital.id}">${hospital.name}</option>`;
    }
  });

  document.getElementById('hospital').innerHTML = options;
}

async function updateDoctorOptions() {
  const hospitalId = document.getElementById('hospital').value;
  const response = await fetch(`/getDoctors.php?hospital_id=${hospitalId}`);
  const doctors = await response.json();

  let options = '';
  doctors.forEach(doctor => {
    options += `<option value="${doctor.id}">${doctor.name}</option>`;
  });

  document.getElementById('doctor_name').innerHTML = options;
}
