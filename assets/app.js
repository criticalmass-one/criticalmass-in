//import './styles/app.css';
import './scss/criticalmass.scss';
import 'dropzone/dist/dropzone.css';

//window.bootstrap = bootstrap;
require('bootstrap');

import GeocodingButton from './js/GeocodingButton';
import Map from './js/Map';
import DataTable from './js/DataTable';
import Search from './js/Search';
import HintModal from './js/HintModal';
import RideDateChecker from './js/RideDateChecker';
import PhotoUpload from './js/PhotoUpload';
import DeleteModal from './js/util/DeleteModal';

import '@fortawesome/fontawesome-pro/js/fontawesome';
import '@fortawesome/fontawesome-pro/js/solid';
import '@fortawesome/fontawesome-pro/js/regular';

export {DeleteModal, PhotoUpload, RideDateChecker, GeocodingButton, Map, DataTable, Search, HintModal};
