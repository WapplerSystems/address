class AddressMap {
  constructor(mapField) {
    this.mapField = mapField;
    this.latitudeField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-latitude-field')+'"]');
    this.longitudeField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-longitude-field')+'"]');
    this.latitudeControlField = document.querySelector('[name="'+ mapField.getAttribute('data-latitude-control-field')+'"]');
    this.longitudeControlField = document.querySelector('[name="'+ mapField.getAttribute('data-longitude-control-field')+'"]');
    this.geocodeButton = document.getElementById(mapField.getAttribute('data-geocode-button'));
    this.addressField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-address-field')+'"]');
    this.cityField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-city-field')+'"]');
    this.zipcodeField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-zipcode-field')+'"]');
    this.countryField = document.querySelector('[data-formengine-input-name="'+ mapField.getAttribute('data-country-field')+'"]');
    this.latitude = this.latitudeField.value;
    this.longitude = this.longitudeField.value;
    this.init();
  }

  init() {
    this.origin = new google.maps.LatLng(this.latitude, this.longitude);
    var myOptions = {
      zoom: 12,
      center: this.origin,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      mapId: this.mapField.getAttribute('id')
    };
    this.map = new google.maps.Map(this.mapField, myOptions);
    this.marker = new google.maps.marker.AdvancedMarkerElement({
      map: this.map,
      position: this.origin,
      draggable: true
    });
    google.maps.event.addListener(this.marker, 'dragend', () => {
      var lat = this.marker.getPosition().lat().toFixed(6);
      var lng = this.marker.getPosition().lng().toFixed(6);
      this.updateValue(this.latitudeField, lat, this.latitudeControlField);
      this.updateValue(this.longitudeField, lng, this.longitudeControlField);
      this.reverseGeocode(lat, lng);
      var position = document.getElementById(this.addressId);
      position.value = lat + "," + lng;
      this.positionChanged();
    });
    this.geocoder = new google.maps.Geocoder();
    this.geocodeButton.addEventListener('click', (e) => {
      e.preventDefault();
      this.codeAddress();
    });

  }

  codeAddress() {
    var address = this.addressField.value;
    let city = this.cityField.value;
    let zipcode = this.zipcodeField.value;
    let country = this.countryField.value;
    if (zipcode) {
      address += ', ' + zipcode;
    }
    if (city) {
      address += ', ' + city;
    }
    if (country) {
      address += ', ' + country;
    }
    console.debug(address);
    var lat = 0;
    var lng = 0;
    if (address.match(/^(-?\d+(\.\d+)?),\s*(-?\d+(\.\d+)?)$/)) {
      lat = address.substr(0, address.lastIndexOf(',')).trim();
      lng = address.substr(address.lastIndexOf(',')+1).trim();
      var position = new google.maps.LatLng(lat, lng);
      this.map.setCenter(position);
      this.marker.position = position;
      this.updateValue(this.latitudeField, lat, this.latitudeControlField);
      this.updateValue(this.longitudeField, lng, this.longitudeControlField);
      this.reverseGeocode(lat, lng);
    } else {
      this.geocoder.geocode({'address': address}, (results, status) => {
        if (status == google.maps.GeocoderStatus.OK) {
          lat = results[0].geometry.location.lat().toFixed(6);
          lng = results[0].geometry.location.lng().toFixed(6);
          var arrAddress = results[0].address_components;
          var route='';
          var streetNumber='';
          arrAddress.forEach(function (address_component) {
            if (address_component.types[0] == "route"){
              route = address_component.long_name;
            }
            if (address_component.types[0] == "street_number"){
              streetNumber = address_component.long_name;
            }
          });
          var formatedAddress = route + ' ' +streetNumber;
          this.map.setCenter(results[0].geometry.location);
          this.marker.position = results[0].geometry.location;
          this.updateValue(this.latitudeField, lat, this.latitudeControlField);
          this.updateValue(this.longitudeField, lng, this.longitudeControlField);
          this.updateValue(this.addressField, formatedAddress);
          this.positionChanged();
        } else {
          alert("Geocode was not successful for the following reason: " + status);
        }
      });
    }
  }

  reverseGeocode(latitude, longitude) {
    var latlng = new google.maps.LatLng(latitude, longitude);
    this.geocoder.geocode({'latLng': latlng}, (results, status) => {
      if (status == google.maps.GeocoderStatus.OK && results[1]) {
        this.updateValue(this.addressField, results[1].formatted_address);
        this.positionChanged();
      }
    });
  }

  updateValue(field, value, controlField) {
    field.value = value;
    field.dispatchEvent(new Event('change', {bubbles: true, cancelable: true}));
    field.value = value;
    if (controlField) {
      let controlFieldName = controlField.getAttribute('name');
      controlField.checked = true;
      document.querySelector('[name="' + controlFieldName + '"][type="hidden"]').value = 1;
      document.querySelector('[name="' + controlFieldName + '"]').parentElement.parentElement.parentElement.parentElement.className.replace('disabled','');
    }
  }

  positionChanged() {
    TYPO3.FormEngine.Validation.validate();
  }
}

window.addEventListener('load', function() {
  var mapFields = document.querySelectorAll('.tx_address_map');
  mapFields.forEach(function(field) {
    new AddressMap(field);
  });
});
