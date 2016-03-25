<?php

namespace app\models;

/**
 * TranslationBuilder is a builder used by the translate service
 *
 * @author adityamittal
 */
class TranslationBuilder {

  public $id; //translation id
  public $inputLanguageCode = 'en';
  public $inputLanguage = "English";
  public $languageCode; //code of output language
  public $language; //name of output language
  public $environment; //dev, it, qa
  public $platform; //portal, client, op etc.
  public $project; //project such as sprintportal, zactportal, myaccount
  public $word; //word to be translated
  public $message; //message to be translated
  public $file; //file to be translated
  public $messages_dir; //the yii messages directory
  public $messages_file; //yii messages file
  public $messages_file_type; //yii messages file i18n_type such as 'PHP_ARRAY' or 'PO'
  public $translateWord; //boolean for translating individual word
  public $translateMessage; //boolean for translating a message
  public $translateFile; //boolean for translating a message file
  public $scope; //scope of the translation such as global
  public $service; //the service to use for getting the translation such as transifex
  public $resourceName; //the resource name
  public $resourceSlug; //the resource slug - use slugifyResource function in this class to generate from resourceName when creating resources
  public $reviewedOnly; //for getting translations, get reviewed translations only
  public $required = array(); //array of required fields

  /**
   * Some default settings for transifex to convert message files for yii
   */
  public function setDefaults() {
    $this->inputLanguageCode = 'en';  //default input language is English
    $this->inputLanguage = "English";
    $this->languageCode = 'en'; //code of output language
    $this->language = 'English'; //name of output language
    $this->environment = array('dev'); //dev, it, qa, 
    $this->platform = 'portal'; //portal, client, op etc.
    $this->project = 'sprintportal'; //project in transifex to use
    $this->messages_dir = 'src/protected/messages/'.$this->inputLanguageCode; //the yii messages directory
    $this->messages_file = ''; //yii messages file
    $this->messages_file_type = 'PHP_ARRAY'; //yii messages file i18n_type such as 'PHP_ARRAY' or 'PO'
    $this->translateWord = false; //boolean for translating individual word
    $this->translateMessage = false; //boolean for translating a message
    $this->translateFile = true; //boolean for translating a message file
    $this->scope = 'global'; //scope of the translation such as global
    $this->service = 'transifex'; //the service to use for getting the translation such as transifex
  }
  
  public function slugifyResource() {
    $this->resourceSlug = $this->slugify($this->resourceName);
  }
  
  /**
   * Translation services like transifex need resource names to be slugified
   * @param type $text
   * @return string
   */
  private function slugify($text) {
    
    $text = preg_replace('~[^\\pL\d]+~u', '-', $text); // replace non letter or digits by -
    $text = trim($text, '-'); // trim
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text); // transliterate    
    $text = strtolower($text); // lowercase
    $text = preg_replace('~[^-\w]+~', '', $text); // remove unwanted characters

    if (empty($text)) {
      return 'n-a';
    }

    return $text;
  }

  public function getLanguageCodes() {
    return array(
      'aa' => 'Afar',
      'ab' => 'Abkhaz',
      'ae' => 'Avestan',
      'af' => 'Afrikaans',
      'ak' => 'Akan',
      'am' => 'Amharic',
      'an' => 'Aragonese',
      'ar' => 'Arabic',
      'as' => 'Assamese',
      'av' => 'Avaric',
      'ay' => 'Aymara',
      'az' => 'Azerbaijani',
      'ba' => 'Bashkir',
      'be' => 'Belarusian',
      'bg' => 'Bulgarian',
      'bh' => 'Bihari',
      'bi' => 'Bislama',
      'bm' => 'Bambara',
      'bn' => 'Bengali',
      'bo' => 'Tibetan Standard, Tibetan, Central',
      'br' => 'Breton',
      'bs' => 'Bosnian',
      'ca' => 'Catalan; Valencian',
      'ce' => 'Chechen',
      'ch' => 'Chamorro',
      'co' => 'Corsican',
      'cr' => 'Cree',
      'cs' => 'Czech',
      'cu' => 'Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic',
      'cv' => 'Chuvash',
      'cy' => 'Welsh',
      'da' => 'Danish',
      'de' => 'German',
      'dv' => 'Divehi; Dhivehi; Maldivian;',
      'dz' => 'Dzongkha',
      'ee' => 'Ewe',
      'el' => 'Greek, Modern',
      'en' => 'English',
      'eo' => 'Esperanto',
      'es' => 'Spanish; Castilian',
      'et' => 'Estonian',
      'eu' => 'Basque',
      'fa' => 'Persian',
      'ff' => 'Fula; Fulah; Pulaar; Pular',
      'fi' => 'Finnish',
      'fj' => 'Fijian',
      'fo' => 'Faroese',
      'fr' => 'French',
      'fy' => 'Western Frisian',
      'ga' => 'Irish',
      'gd' => 'Scottish Gaelic; Gaelic',
      'gl' => 'Galician',
      'gn' => 'GuaranÃ­',
      'gu' => 'Gujarati',
      'gv' => 'Manx',
      'ha' => 'Hausa',
      'he' => 'Hebrew (modern)',
      'hi' => 'Hindi',
      'ho' => 'Hiri Motu',
      'hr' => 'Croatian',
      'ht' => 'Haitian; Haitian Creole',
      'hu' => 'Hungarian',
      'hy' => 'Armenian',
      'hz' => 'Herero',
      'ia' => 'Interlingua',
      'id' => 'Indonesian',
      'ie' => 'Interlingue',
      'ig' => 'Igbo',
      'ii' => 'Nuosu',
      'ik' => 'Inupiaq',
      'io' => 'Ido',
      'is' => 'Icelandic',
      'it' => 'Italian',
      'iu' => 'Inuktitut',
      'ja' => 'Japanese (ja)',
      'jv' => 'Javanese (jv)',
      'ka' => 'Georgian',
      'kg' => 'Kongo',
      'ki' => 'Kikuyu, Gikuyu',
      'kj' => 'Kwanyama, Kuanyama',
      'kk' => 'Kazakh',
      'kl' => 'Kalaallisut, Greenlandic',
      'km' => 'Khmer',
      'kn' => 'Kannada',
      'ko' => 'Korean',
      'kr' => 'Kanuri',
      'ks' => 'Kashmiri',
      'ku' => 'Kurdish',
      'kv' => 'Komi',
      'kw' => 'Cornish',
      'ky' => 'Kirghiz, Kyrgyz',
      'la' => 'Latin',
      'lb' => 'Luxembourgish, Letzeburgesch',
      'lg' => 'Luganda',
      'li' => 'Limburgish, Limburgan, Limburger',
      'ln' => 'Lingala',
      'lo' => 'Lao',
      'lt' => 'Lithuanian',
      'lu' => 'Luba-Katanga',
      'lv' => 'Latvian',
      'mg' => 'Malagasy',
      'mh' => 'Marshallese',
      'mi' => 'Maori',
      'mk' => 'Macedonian',
      'ml' => 'Malayalam',
      'mn' => 'Mongolian',
      'mr' => 'Marathi (Mara?hi)',
      'ms' => 'Malay',
      'mt' => 'Maltese',
      'my' => 'Burmese',
      'na' => 'Nauru',
      'nb' => 'Norwegian BokmÃ¥l',
      'nd' => 'North Ndebele',
      'ne' => 'Nepali',
      'ng' => 'Ndonga',
      'nl' => 'Dutch',
      'nn' => 'Norwegian Nynorsk',
      'no' => 'Norwegian',
      'nr' => 'South Ndebele',
      'nv' => 'Navajo, Navaho',
      'ny' => 'Chichewa; Chewa; Nyanja',
      'oc' => 'Occitan',
      'oj' => 'Ojibwe, Ojibwa',
      'om' => 'Oromo',
      'or' => 'Oriya',
      'os' => 'Ossetian, Ossetic',
      'pa' => 'Panjabi, Punjabi',
      'pi' => 'Pali',
      'pl' => 'Polish',
      'ps' => 'Pashto, Pushto',
      'pt' => 'Portuguese',
      'qu' => 'Quechua',
      'rm' => 'Romansh',
      'rn' => 'Kirundi',
      'ro' => 'Romanian, Moldavian, Moldovan',
      'ru' => 'Russian',
      'rw' => 'Kinyarwanda',
      'sa' => 'Sanskrit (Sa?sk?ta)',
      'sc' => 'Sardinian',
      'sd' => 'Sindhi',
      'se' => 'Northern Sami',
      'sg' => 'Sango',
      'si' => 'Sinhala, Sinhalese',
      'sk' => 'Slovak',
      'sl' => 'Slovene',
      'sm' => 'Samoan',
      'sn' => 'Shona',
      'so' => 'Somali',
      'sq' => 'Albanian',
      'sr' => 'Serbian',
      'ss' => 'Swati',
      'st' => 'Southern Sotho',
      'su' => 'Sundanese',
      'sv' => 'Swedish',
      'sw' => 'Swahili',
      'ta' => 'Tamil',
      'te' => 'Telugu',
      'tg' => 'Tajik',
      'th' => 'Thai',
      'ti' => 'Tigrinya',
      'tk' => 'Turkmen',
      'tl' => 'Tagalog',
      'tn' => 'Tswana',
      'to' => 'Tonga (Tonga Islands)',
      'tr' => 'Turkish',
      'ts' => 'Tsonga',
      'tt' => 'Tatar',
      'tw' => 'Twi',
      'ty' => 'Tahitian',
      'ug' => 'Uighur, Uyghur',
      'uk' => 'Ukrainian',
      'ur' => 'Urdu',
      'uz' => 'Uzbek',
      've' => 'Venda',
      'vi' => 'Vietnamese',
      'vo' => 'VolapÃ¼k',
      'wa' => 'Walloon',
      'wo' => 'Wolof',
      'xh' => 'Xhosa',
      'yi' => 'Yiddish',
      'yo' => 'Yoruba',
      'za' => 'Zhuang, Chuang',
      'zh' => 'Chinese',
      'zu' => 'Zulu'
    );
  }

}
