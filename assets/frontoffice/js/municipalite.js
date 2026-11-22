(() => {
  'use strict';

  const mapNode = document.getElementById('delegation-map');
  const governorate = document.getElementById('gouvernorat');
  const delegation = document.getElementById('delegation');

  if (mapNode && governorate && delegation) {
    let map = {};
    try {
      map = JSON.parse(mapNode.textContent || '{}');
    } catch (error) {
      console.error('La liste des délégations est illisible.', error);
    }

    const refreshDelegations = () => {
      const selected = governorate.value;
      delegation.replaceChildren();
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = selected ? 'Choisir une délégation' : 'Choisir d’abord un gouvernorat';
      delegation.append(placeholder);
      const options = Array.isArray(map[selected]) ? map[selected] : [];
      options.forEach((name) => {
        const option = document.createElement('option');
        option.value = name;
        option.textContent = name;
        delegation.append(option);
      });
      delegation.disabled = options.length === 0;
    };

    governorate.addEventListener('change', refreshDelegations);
    refreshDelegations();
  }

  const birthType = document.getElementById('birth-type');
  if (birthType) {
    const declarantFields = document.querySelectorAll('.birth-declarant');
    const courtFields = document.querySelectorAll('.birth-tribunal');
    const updateBirthFields = () => {
      const isCourt = birthType.value === 'jugement';
      declarantFields.forEach((wrapper) => {
        wrapper.classList.toggle('d-none', isCourt);
        wrapper.querySelectorAll('input').forEach((input) => { input.required = !isCourt; });
      });
      courtFields.forEach((wrapper) => {
        wrapper.classList.toggle('d-none', !isCourt);
        wrapper.querySelectorAll('input').forEach((input) => { input.required = isCourt; });
      });
    };
    birthType.addEventListener('change', updateBirthFields);
    updateBirthFields();
  }

  const celebration = document.getElementById('celebration');
  if (celebration) {
    const notaryFields = document.querySelectorAll('.notary');
    const updateNotaryFields = () => {
      const showNotaries = ['notaires', 'municipalite_et_notaires'].includes(celebration.value);
      notaryFields.forEach((wrapper) => {
        wrapper.classList.toggle('d-none', !showNotaries);
        wrapper.querySelectorAll('input').forEach((input) => { input.required = showNotaries; });
      });
    };
    celebration.addEventListener('change', updateNotaryFields);
    updateNotaryFields();
  }
})();
