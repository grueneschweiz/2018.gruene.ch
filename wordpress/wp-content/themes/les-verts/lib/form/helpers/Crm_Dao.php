<?php

namespace SUPT;

use Webling\API\Client;
use Webling\API\ClientException;

/**
 * lock out script kiddies: die on direct call
 */
defined( 'ABSPATH' ) or die( 'Better secure than sorry!' );


/**
 * Class Crm_Dao
 *
 * !!! THIS IS A MOCK CLASS, DO NOT USE IT IN PRODUCTION !!!
 *
 * TODO: This class must be adapted to the webling service, as soon as is ready.
 * Until then, this class serves for testing purposes.
 *
 * @package SUPT
 */
class Crm_Dao {
	/**
	 * Date format of the crm
	 */
	const CRM_DATE_FORMAT = 'Y-m-d H:i:s';
	
	/**
	 * API client
	 *
	 * @var
	 */
	private $api;
	
	/**
	 * Crm_Dao constructor.
	 *
	 * @throws ClientException
	 */
	public function __construct() {
		$api_key = \get_field( 'api_key', 'option' );
		$api_url = \get_field( 'api_url', 'option' );
		
		$this->api = new Client( $api_url, $api_key );
	}
	
	public function save( string $email, array $data ) {
		$id = $this->find_by_email( $email );
		
		if ( $id ) {
			$this->update( $id, $data );
		} else {
			$id = $this->insert( $data );
		}
		
		return $id;
	}
	
	private function find_by_email( string $email ) {
		$resp = $this->api->get( 'member/?filter=`E-Mail / courriel 1` = "' . urlencode( $email ) . '"' );
		
		if ( $resp->getStatusCode() >= 400 ) {
			throw new ClientException( "The CRM API responded with status code {$resp->getStatusCode()}" );
		}
		
		if ( 1 === count( $resp->getData()['objects'] ) ) {
			return $resp->getData()['objects'][0];
		}
		
		return false;
	}
	
	private function update( int $id, array $data ) {
		$resp = $this->api->get( "member/$id" );
		
		if ( $resp->getStatusCode() >= 400 ) {
			throw new ClientException( "The CRM API responded with status code {$resp->getStatusCode()}" );
		}
		
		$member = $resp->getData();
		
		$member_data = $this->patch_member( $member['properties'], $data );
		
		$this->api->put( '/member/'.$id, [ 'properties' => $member_data, 'parents' => $member['parents'] ] );
	}
	
	private function patch_member( $target, $data ) {
		// todo: use insertion mode of field config (now replace is applied for all)
		
		$data = $this->replace_webling_keys($data);
		$merged = array_merge($target, $data);
		$diff = array_intersect_key($merged, $data);
		
		return $diff;
	}
	
	private function insert( array $data ) {
		$member_data = $this->patch_member( [], $data );
		$group       = \get_field( 'group_id', 'option' );
		
		$resp = $this->api->post( '/member', [ 'properties' => $member_data, 'parents' => [ $group ] ] );
		
		return $resp->getData();
	}
	
	private function replace_webling_keys( $data ) {
		$mapping = [
			'firstName'                => 'Vorname / prénom',
			'lastName'                 => 'Name / nom',
			'recordCategory'           => 'Datensatzkategorie / type d’entrée',
			'language'                 => 'Sprache / langue',
			'salutationInformal'       => 'Anrede / appel (informel)',
			'address1'                 => 'Strasse / rue',
			'zip'                      => 'PLZ / code postal',
			'city'                     => 'Ort / localité',
			'email1'                   => 'E-Mail / courriel 1',
			'mobilePhone'              => 'Mobile / mobile',
			'newsletterCountryD'       => 'Newsletter national DE',
			'newsletterCountryF'       => 'infolettre nationale FR',
			'newsletterCantonD'        => 'Newsletter kantonal DE',
			'newsletterCantonF'        => 'infolettre cantonale FR',
			'newsletterMunicipality'   => 'Newsletter Kommunal / infolettre communale',
			'pressReleaseCountryD'     => 'MM National DE',
			'pressReleaseCountryF'     => 'CP national FR',
			'pressReleaseCantonD'      => 'MM Kantonal DE',
			'pressReleaseCantonF'      => 'CP cantonal FR',
			'pressReleaseMunicipality' => 'MM Kommunal / CP communal',
			'memberStatusCountry'      => 'Mitgliedschaft National / affiliation nationale',
			'memberStatusCanton'       => 'Mitgliedschaft Kantonal / affiliation cantonale',
			'memberStatusRegion'       => 'Mitgliedschaft Bezirk / affiliation d’un district',
			'memberStatusMunicipality' => 'Mitgliedschaft Kommunal / affiliation communale',
			'memberStatusYoung'        => 'Mitgliedschaft Junge Grüne / affiliation Jeunes Verts',
			'entryChannel'             => 'Gewinnungskanal / acquisition',
			'interests'                => 'Interessen / intérêts',
			'request'                  => 'Anfragen für / disponibilités',
			'profession'               => 'Beruf / profession',
			'notesCountry'             => 'Notizen National / notes nationales',
			'notesCanton'              => 'Notizen Kanton / notes cantonales',
			'notesMunicipality'        => 'Notizen Kommunal / notes communales',
		];
		
		foreach ( $data as $key => $value ) {
			$data[$mapping[$key]]=$value;
			unset($data[$key]);
		}
		
		return $data;
	}
}
