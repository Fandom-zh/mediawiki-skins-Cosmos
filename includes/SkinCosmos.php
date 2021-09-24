<?php

namespace MediaWiki\Skin\Cosmos;

use Config;
use ConfigFactory;
use ExtensionRegistry;
use MediaWiki\Languages\LanguageNameUtils;
use MediaWiki\Permissions\PermissionManager;
use MediaWiki\SpecialPage\SpecialPageFactory;
use MediaWiki\User\UserOptionsLookup;
use OutputPage;
use SkinTemplate;
use TitleFactory;

class SkinCosmos extends SkinTemplate {

	/** @var Config */
	public $config;

	/** @var CosmosConfig */
	public $cosmosConfig;

	/** @var LanguageNameUtils */
	public $languageNameUtils;

	/** @var PermissionManager */
	public $permissionManager;

	/** @var SpecialPageFactory */
	public $specialPageFactory;

	/** @var TitleFactory */
	public $titleFactory;

	/** @var UserOptionsLookup */
	public $userOptionsLookup;

	/** @var CosmosWordmarkLookup */
	public $wordmarkLookup;

	/**
	 * @param ConfigFactory $configFactory
	 * @param CosmosConfig $cosmosConfig
	 * @param CosmosWordmarkLookup $cosmosWordmarkLookup
	 * @param LanguageNameUtils $languageNameUtils
	 * @param PermissionManager $permissionManager
	 * @param SpecialPageFactory $specialPageFactory
	 * @param TitleFactory $titleFactory
	 * @param UserOptionsLookup $userOptionsLookup
	 * @param array $options
	 */
	public function __construct(
		ConfigFactory $configFactory,
		CosmosConfig $cosmosConfig,
		CosmosWordmarkLookup $cosmosWordmarkLookup,
		LanguageNameUtils $languageNameUtils,
		PermissionManager $permissionManager,
		SpecialPageFactory $specialPageFactory,
		TitleFactory $titleFactory,
		UserOptionsLookup $userOptionsLookup,
		array $options
	) {
		parent::__construct( $options );

		$this->config = $configFactory->makeConfig( 'Cosmos' );
		$this->cosmosConfig = $cosmosConfig;
		$this->languageNameUtils = $languageNameUtils;
		$this->permissionManager = $permissionManager;
		$this->specialPageFactory = $specialPageFactory;
		$this->titleFactory = $titleFactory;
		$this->userOptionsLookup = $userOptionsLookup;
		$this->wordmarkLookup = $cosmosWordmarkLookup;
	}

	/**
	 * @param OutputPage $out
	 */
	public function initPage( OutputPage $out ) {
		if (
			$this->userOptionsLookup->getBoolOption(
				$this->getUser(), 'cosmos-mobile-responsiveness'
			)
		) {
			$out->addMeta(
				'viewport',
				'width=device-width, initial-scale=1.0, ' .
				'user-scalable=yes, minimum-scale=0.25, maximum-scale=5.0'
			);
		}
	}

	/**
	 * @return array
	 */
	public function getDefaultModules() {
		$modules = parent::getDefaultModules();

		// CosmosRail styles
		if ( ( CosmosRail::railsExist( $this->cosmosConfig, $this->getContext() ) ||
				CosmosRail::hookRailsExist( $this->cosmosConfig, $this->getContext() )
			) &&
			!CosmosRail::railsHidden( $this->cosmosConfig, $this->getContext() )
		) {
			$modules['styles']['skin'][] = 'skins.cosmos.rail';
		}

		// Load PortableInfobox styles
		if ( ExtensionRegistry::getInstance()->isLoaded( 'Portable Infobox' ) ) {
			$modules['styles']['skin'][] = 'skins.cosmos.portableinfobox';

			// Load PortableInfobox EuropaTheme style if the configuration is enabled
			if ( $this->config->get( 'CosmosEnablePortableInfoboxEuropaTheme' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.portableinfobox.europa';
			} else {
				$modules['styles']['skin'][] = 'skins.cosmos.portableinfobox.default';
			}
		}

		if (
			LessUtil::isThemeDark( 'content-background-color' ) &&
			ExtensionRegistry::getInstance()->isLoaded( 'CodeMirror' ) &&
			ExtensionRegistry::getInstance()->isLoaded( 'VisualEditor' )
		) {
			$modules['styles']['skin'][] = 'skins.cosmos.codemirror';
		}

		// Load SocialProfile styles if the respective configuration variables are enabled
		if ( class_exists( 'UserProfilePage' ) ) {
			if ( $this->config->get( 'CosmosSocialProfileModernTabs' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.profiletabs';
			}

			if ( $this->config->get( 'CosmosSocialProfileRoundAvatar' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.profileavatar';
			}

			if ( $this->config->get( 'CosmosSocialProfileShowEditCount' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.profileeditcount';
			}

			if ( $this->config->get( 'CosmosSocialProfileAllowBio' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.profilebio';
			}

			if ( $this->config->get( 'CosmosSocialProfileShowGroupTags' ) ) {
				$modules['styles']['skin'][] = 'skins.cosmos.profiletags';
			}

			if (
				$this->config->get( 'CosmosSocialProfileModernTabs' ) ||
				$this->config->get( 'CosmosSocialProfileRoundAvatar' ) ||
				$this->config->get( 'CosmosSocialProfileShowEditCount' ) ||
				$this->config->get( 'CosmosSocialProfileAllowBio' ) ||
				$this->config->get( 'CosmosSocialProfileShowGroupTags' )
			) {
				$modules['styles']['skin'][] = 'skins.cosmos.socialprofile';
			}
		}

		return $modules;
	}
}
