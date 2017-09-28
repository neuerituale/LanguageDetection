<?php
/**
 * COPYRIGHT NOTICE
 *
 * Copyright (c) 2017 Neue Rituale GbR
 * @author Julian Winkel <code@neuerituale.com>
 * @version 1.0.0
 * @license MIT
 *
 * This file is part of the LanguageDetection project.
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace NR\Utilities;

class LanguageDetection
{

	/**
	 * Fallback Language Iso2
	 * @var string
	 */
	public $fallbackLanguageIso2 = 'en';

	/**
	 * Available Language Iso2 codes
	 * @var array
	 */
	public $availableLanguages = array();

	/**
	 * Enable Cookie Support
	 * @var bool
	 */
	public $cookieSupport = true;

	/**
	 * Cookie Name
	 * @var string
	 */
	public $cookieName = 'LanguageDetection';
	public $cookieExpire = 0;
	public $cookiePath = '/';
	public $cookieDomain = '';
	public $cookieSecure = false;
	public $cookieHttponly = false;

	/**
	 * LanguageDetection constructor.
	 * @param array $availableLanguages
	 * @param string $fallbackLanguageIso2
	 */
	public function __construct($availableLanguages, $fallbackLanguageIso2 = '') {

		// set available Languages Array
		if(is_array($availableLanguages)){
			$this->availableLanguages = $availableLanguages;
		}

		// overwrite Fallback
		if($fallbackLanguageIso2){
			$this->fallbackLanguageIso2 = $fallbackLanguageIso2;
		}
	}

	/**
	 * Find the correct Language
	 * 1. From Cookie
	 * 2. From Header match with availableLangauge Array
	 *
	 * @return string
	 */
	public function findLanguage(){

		// Cookie Language
		$cookieLanguage = ( array_key_exists($this->cookieName, $_COOKIE) ) ? substr(strip_tags($_COOKIE[ $this->cookieName ]), 0, 2) : '';

		if($this->cookieSupport && !empty($cookieLanguage)) {

			// return iso2 if is in array;
			return (in_array($cookieLanguage, $this->availableLanguages)) ? (string) $cookieLanguage : $this->fallbackLanguageIso2;
		}

		else {

			// accepted Langauges from header
			$acceptedLanguages = $this->getAcceptedLanguage();

			// Iterate all Languages from "acceptedLanguage"-Header
			foreach($acceptedLanguages as $iso2){

				$iso2 = strtolower($iso2);

				// if isocode are de-de or en-gb
				// the first digits are the Language
				// https://en.wikipedia.org/wiki/Locale_(computer_software)
				if(preg_match('/^[a-z]{2}-[a-z]{2}/i', $iso2) === 1){
					$iso2 = substr($iso2, 0, 2);
				}

				if(in_array($iso2, $this->availableLanguages)){
					return (string) $iso2;
				}
			}

			// fallback
			return $this->fallbackLanguageIso2;

		}
	}

	/**
	 * Set Language Iso2 in Cookie
	 *
	 * @param string $languageIso2
	 * @return string
	 */
	public function setLanguage($languageIso2){
		if($this->cookieSupport){

			if(!in_array($languageIso2, $this->availableLanguages)){
				$languageIso2 = $this->fallbackLanguageIso2;
			}

			// set cookie
			setcookie ($this->cookieName, $languageIso2, $this->cookieExpire, $this->cookiePath, $this->cookieDomain, $this->cookieSecure, $this->cookieHttponly);
			$_COOKIE[$this->cookieName] = $languageIso2;

			return $languageIso2;

		} else {
			return '';
		}
	}

	/**
	 * remove cookie
	 */
	public function unsetLanguage(){
		if(isset($_COOKIE[$this->cookieName])) {
			unset($_COOKIE[$this->cookieName]);
			setcookie($this->cookieName, null, -1);
		}
	}

	/**
	 * Get the Accepted Language from Browser Header
	 * Array is sorted with the quality-parameter (;q=) from 1 to 0
	 * remove special character
	 *
	 * @return array
	 */
	public function getAcceptedLanguage() {
		$languagesArr = array();

		$acceptLanguageHeader = self::trimExplode(
			',',
			(isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : ''
		);

		// create Array with quality parameter
		$acceptedLanguages = array();
		foreach ($acceptLanguageHeader as $key => $acceptLanguage) {
			@list($languageCode, $quality) = self::trimExplode(';', $acceptLanguage);
			$acceptedLanguages[$languageCode] = $quality ? (float)substr($quality, 2) : (float)1;
		}

		// Now sort the accepted languages by their quality and create an array containing only the language codes in the correct order.
		if (count($acceptedLanguages)) {

			arsort($acceptedLanguages);
			$languageCodesArr = array_keys($acceptedLanguages);
			if (is_array($languageCodesArr)) {
				foreach ($languageCodesArr as $languageCode) {
					$languageCode = preg_replace("/[^a-z0-9ÄäÖöÜü\-]/i", '', $languageCode);
					$languagesArr[] = $languageCode;
				}
			}
		} else {
			//trigger_error('Header \'HTTP_ACCEPT_LANGUAGE\' is not set or incorrect!', E_USER_NOTICE);
		}

		return $languagesArr;
	}

	/**
	 * @param $delim
	 * @param $string
	 * @return array
	 */
	private static function trimExplode($delim, $string){
		$result = explode($delim, $string);
		return array_map('trim', $result);
	}

}