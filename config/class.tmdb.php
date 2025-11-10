<?php
use Symfony\Component\HttpClient\HttpClient;

// https://developer.themoviedb.org/reference/intro/getting-started

class TMDB
{
	const IMAGE_BACKDROP = 'backdrop';
	const IMAGE_POSTER = 'poster';
	const IMAGE_PROFILE = 'profile';

	protected $language;
	protected $languageISO;
	protected $config;

	public function __construct(string $language = 'fr-FR', string $languageISO = 'fr,en,null', bool $config = false)
	{
		$this->setLanguage($language);
		$this->setLanguageISO($languageISO);

		if($config === true)
			$this->getConfiguration();
	}

	/**
	 * Certifications
	 */

	// https://developer.themoviedb.org/reference/certification-movie-list
	public function getMovieCertifications()
	{
		return $this->makeCall('certification/movie/list');
	}

	// https://developer.themoviedb.org/reference/certification-movie-list
	public function getTvCertifications()
	{
		return $this->makeCall('certification/tv/list');
	}

	/**
	 * Changes
	 */

	// https://developer.themoviedb.org/reference/changes-movie-list
	public function getChangesMovieList(?string $end_date = null, ?int $page = 1, ?string $start_date = null)
	{
		$params = [
			'end_date' => (string) $end_date,
			'page' => (int) $page,
			'start_date' => (string) $start_date
		];

		return $this->makeCall('movie/changes', $params);
	}

	// https://developer.themoviedb.org/reference/changes-people-list
	public function getChangesPersonList(?string $end_date = null, ?int $page = 1, ?string $start_date = null)
	{
		$params = [
			'end_date' => (string) $end_date,
			'page' => (int) $page,
			'start_date' => (string) $start_date
		];

		return $this->makeCall('person/changes', $params);
	}

	// https://developer.themoviedb.org/reference/changes-tv-list
	public function getChangesTvList(?string $end_date = null, ?int $page = 1, ?string $start_date = null)
	{
		$params = [
			'end_date' => (string) $end_date,
			'page' => (int) $page,
			'start_date' => (string) $start_date
		];

		return $this->makeCall('tv/changes', $params);
	}

	/**
	 * Companies
	 */

	// https://developer.themoviedb.org/reference/company-details

	public function getCompaniesDetails(int $company_id)
	{
		return $this->makeCall('company/'.$company_id);
	}

	// https://developer.themoviedb.org/reference/company-alternative-names

	public function getCompaniesAlternativeNames(int $company_id)
	{
		return $this->makeCall('company/'.$company_id.'/alternative_names');
	}

	// https://developer.themoviedb.org/reference/company-images

	public function getCompaniesImages(int $company_id)
	{
		return $this->makeCall('company/'.$company_id.'/images');
	}

	/**
	 * Configuration
	 */

	// https://developer.themoviedb.org/reference/configuration-details
	public function getConfigurationDetails()
	{
		return $this->makeCall('configuration');
	}

	// https://developer.themoviedb.org/reference/configuration-countries
	public function getConfigurationCountries(?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('configuration/countries', $params);
	}

	// https://developer.themoviedb.org/reference/configuration-jobs
	public function getConfigurationJobs()
	{
		return $this->makeCall('configuration/jobs');
	}

	// https://developer.themoviedb.org/reference/configuration-languages
	public function getConfigurationLanguages()
	{
		return $this->makeCall('configuration/languages');
	}

	// https://developer.themoviedb.org/reference/configuration-primary-translations
	public function getConfigurationPrimaryTranslations()
	{
		return $this->makeCall('configuration/primary_translations');
	}

	// https://developer.themoviedb.org/reference/configuration-timezones
	public function getConfigurationTimezones()
	{
		return $this->makeCall('configuration/timezones');
	}

	/**
	 * Discover
	 */

	// https://developer.themoviedb.org/reference/discover-movie
	public function getMovieDiscover(
		?string $certification = null,
		?string $certificationgte = null, // supérieur ou égal
		?string $certificationlte = null, // inférieur ou égal
		?string $certification_country = null,
			?bool $include_adult = null, // Defaults to false
			?bool $include_video = null, // Defaults to false
			?string $language = null, // Defaults to en-US
			?int $page = null, // Defaults to 1
		?int $primary_release_year = null,
		?string $primary_release_dategte = null, // supérieur ou égal
		?string $primary_release_datelte = null, // inférieur ou égal
		?string $region = null,
		?string $release_dategte = null, // supérieur ou égal
		?string $release_datelte = null, // inférieur ou égal
			?string $sort_by = 'popularity.desc', // Defaults to popularity.desc - first_air_date.asc, first_air_date.desc, name.asc, name.desc, original_name.asc, original_name.desc, popularity.asc, popularity.desc, vote_average.asc, vote_average.desc, vote_count.asc, vote_count.desc
		?float $vote_averagegte = null, // supérieur ou égal
		?float $vote_averagelte = null, // inférieur ou égal
		?float $vote_countgte = null, // supérieur ou égal
		?float $vote_countlte = null, // inférieur ou égal
		?string $watch_region = null,
		?string $with_cast = null,
		?string $with_companies = null,
		?string $with_crew = null,
		?string $with_genres = null,
		?string $with_keywords = null,
		?string $with_origin_country = null,
		?string $with_original_language = null,
		?string $with_people = null,
		?int $with_release_type = null,
		?int $with_runtimegte = null, // supérieur ou égal
		?int $with_runtimelte = null, // inférieur ou égal
		?string $with_watch_monetization_types = null,
		?string $with_watch_providers = null,
		?string $without_companies = null,
		?string $without_genres = null,
		?string $without_keywords = null,
		?string $without_watch_providers = null,
		?int $year = null
	)
	{
		$params = [
			'certification' => (string) $certification, // use in conjunction with region
			'certification.gte' => (string) $certificationgte, // use in conjunction with region
			'certification.lte' => (string) $certificationlte, // use in conjunction with region
			'certification_country' => (string) $certification_country, // use in conjunction with the certification, certification.gte and certification.lte filters
			'include_adult' => (bool) $include_adult,
			'include_video' => (bool) $include_video,
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page,
			'primary_release_year' => (string) $primary_release_year,
			'primary_release_date.gte' => (string) $primary_release_dategte,
			'primary_release_date.lte' => (string) $primary_release_datelte,
			'region' => (string) $region,
			'release_date.gte' => (string) $release_dategte,
			'release_date.lte' => (string) $release_datelte,
			'sort_by' => (string) $sort_by,
			'vote_average.gte' => (string) $vote_averagegte,
			'vote_average.lte' => (string) $vote_averagelte,
			'vote_count.gte' => (string) $vote_countgte,
			'vote_count.lte' => (string) $vote_countlte,
			'watch_region' => (string) $watch_region, // use in conjunction with with_watch_monetization_types or with_watch_providers
			'with_cast' => (string) $with_cast, // can be a comma (AND) or pipe (OR) separated query
			'with_companies' => (string) $with_companies, // can be a comma (AND) or pipe (OR) separated query
			'with_crew' => (string) $with_crew, // can be a comma (AND) or pipe (OR) separated query
			'with_genres' => (string) $with_genres, // can be a comma (AND) or pipe (OR) separated query
			'with_keywords' => (string) $with_keywords, // can be a comma (AND) or pipe (OR) separated query
			'with_origin_country' => (string) $with_origin_country,
			'with_original_language' => (string) $with_original_language,
			'with_people' => (string) $with_people, // can be a comma (AND) or pipe (OR) separated query
			'with_release_type' => (string) $with_release_type, // can be a comma (AND) or pipe (OR) separated query, can be used in conjunction with region
			'with_runtime.gte' => (string) $with_runtimegte,
			'with_runtime.lte' => (string) $with_runtimelte,
			'with_watch_monetization_types' => (string) $with_watch_monetization_types, // possible values are: [flatrate, free, ads, rent, buy] use in conjunction with watch_region, can be a comma (AND) or pipe (OR) separated query
			'with_watch_providers' => (string) $with_watch_providers, // use in conjunction with watch_region, can be a comma (AND) or pipe (OR) separated query
			'without_companies' => (string) $without_companies, // use in conjunction with watch_region, can be a comma (AND) or pipe (OR) separated query
			'without_genres' => (string) $without_genres,
			'without_keywords' => (string) $without_keywords,
			'without_watch_providers' => (string) $without_watch_providers,
			'year' => (string) $year,
		];

		return $this->makeCall('discover/movie', $params);
	}

	// https://developer.themoviedb.org/reference/discover-tv
	public function getTvDiscover(
		?string $air_dategte = null, // supérieur ou égal
		?string $air_datelte = null, // inférieur ou égal
		?int $first_air_date_year = null,
		?string $first_air_dategte = null, // supérieur ou égal, dont la première diffusion est postérieure ou égale au JOUR MOIS ANNNÉE
		?string $first_air_datelte = null, // inférieur ou égal, dont la première diffusion est antérieure ou égale au JOUR MOIS ANNNÉE
		?bool $include_adult = null, // Defaults to false
			?bool $include_null_first_air_dates = null, // Defaults to false
			?string $language = null, // Defaults to en-US
			?int $page = null, // Defaults to 1
		?bool $screened_theatrically = null,
			?string $sort_by = 'popularity.desc', // Defaults to popularity.desc - first_air_date.asc, first_air_date.desc, name.asc, name.desc, original_name.asc, original_name.desc, popularity.asc, popularity.desc, vote_average.asc, vote_average.desc, vote_count.asc, vote_count.desc
		?string $timezone = null,
		?float $vote_averagegte = null, // supérieur ou égal
		?float $vote_averagelte = null, // inférieur ou égal
		?float $vote_countgte = null, // supérieur ou égal
		?float $vote_countlte = null, // inférieur ou égal
		?string $watch_region = null,
		?string $with_companies = null,
		?string $with_genres = null,
		?string $with_keywords = null,
		?int $with_networks = null,
		?string $with_origin_country = null,
		?string $with_original_language = null,
		?int $with_runtimegte = null, // supérieur ou égal
		?int $with_runtimelte = null, // inférieur ou égal
		?string $with_status = null,
		?string $with_watch_monetization_types = null,
		?string $with_watch_providers = null,
		?string $without_companies = null,
		?string $without_genres = null,
		?string $without_keywords = null,
		?string $without_watch_providers = null,
		?string $with_type = null
	)
	{
		$params = [
			'air_dategte' => (string) $air_dategte,
			'air_datelte' => (string) $air_datelte,
			'first_air_date_year' => (int) $first_air_date_year,
			'first_air_dategte' => (string) $first_air_dategte,
			'first_air_datelte' => (string) $first_air_datelte,
			'include_adult' => (bool) $include_adult,
			'include_null_first_air_dates' => (bool) $include_null_first_air_dates,
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page,
			'screened_theatrically' => (bool) $screened_theatrically,
			'sort_by' => (string) $sort_by,
			'timezone' => (string) $timezone,
			'vote_averagegte' => (float) $vote_averagegte,
			'vote_averagelte' => (float) $vote_averagelte,
			'vote_countgte' => (float) $vote_countgte,
			'vote_countlte' => (float) $vote_countlte,
			'watch_region' => (string) $watch_region, // use in conjunction with with_watch_monetization_types or with_watch_providers
			'with_companies' => (string) $with_companies, // can be a comma (AND) or pipe (OR) separated query
			'with_genres' => (string) $with_genres, // can be a comma (AND) or pipe (OR) separated query
			'with_keywords' => (string) $with_keywords, // can be a comma (AND) or pipe (OR) separated query
			'with_networks' => (int) $with_networks,
			'with_origin_country' => (string) $with_origin_country,
			'with_original_language' => (string) $with_original_language,
			'with_runtimegte' => (int) $with_runtimegte,
			'with_runtimelte' => (int) $with_runtimelte,
			'with_status' => (string) $with_status, // possible values are: [0, 1, 2, 3, 4, 5], can be a comma (AND) or pipe (OR) separated query
			'with_watch_monetization_types' => (string) $with_watch_monetization_types, // possible values are: [flatrate, free, ads, rent, buy] use in conjunction with watch_region, can be a comma (AND) or pipe (OR) separated query
			'with_watch_providers' => (string) $with_watch_providers, // use in conjunction with watch_region, can be a comma (AND) or pipe (OR) separated query
			'without_companies' => (string) $without_companies,
			'without_genres' => (string) $without_genres,
			'without_keywords' => (string) $without_keywords,
			'without_watch_providers' => (string) $without_watch_providers,
			'with_type' => (string) $with_type // possible values are: [0, 1, 2, 3, 4, 5, 6], can be a comma (AND) or pipe (OR) separated query
		];

		return $this->makeCall('discover/tv', $params);
	}

	/**
	 * Find - Trouver par ID externe
	 */

	// https://developer.themoviedb.org/reference/find-by-id
	public function findById(?string $external_id, ?string $external_source, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'external_source' => (in_array($external_source, ['imdb_id', 'facebook_id', 'instagram_id', 'tvdb_id', 'tiktok_id', 'twitter_id', 'wikidata_id', 'youtube_id']) ? $external_source : null)
		];

		return $this->makeCall('find/'.$external_id, $params);
	}

	/**
	 * Genres
	 */

	// https://developer.themoviedb.org/reference/genre-movie-list
	public function getMovieGenres(?string $language = null)
	{
		$params = ['language' => ($language !== null) ? $language : $this->getLanguage()];

		return $this->makeCall('genre/movie/list', $params);
	}

	// https://developer.themoviedb.org/reference/genre-tv-list
	public function getTvGenres(?string $language = null)
	{
		$params = ['language' => ($language !== null) ? $language : $this->getLanguage()];

		return $this->makeCall('genre/tv/list', $params);
	}

	/**
	 * Keywords
	 */

	// https://developer.themoviedb.org/reference/keyword-details
	public function getKeywordDetails(int $keyword_id)
	{
		$params = ['keyword_id' => (int) $keyword_id];

		return $this->makeCall('keyword/'.$keyword_id, $params);
	}

	/**
	 * Movie List
	 */

	// https://developer.themoviedb.org/reference/movie-now-playing-list
	public function getMovieNowPlaying(?string $region = null, ?string $language = null, ?int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'region' => (string) $region,
			'language' => ($language!== null) ? $language : $this->getLanguage()
		];

		return $this->makeCall('movie/now_playing', $params);
	}

	// https://developer.themoviedb.org/reference/movie-popular-list
	public function getMoviePopular(?string $region = null, ?string $language = null, ?int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'region' => (string) $region,
			'language' => ($language!== null) ? $language : $this->getLanguage()
		];

		return $this->makeCall('movie/now_playing', $params);
	}

	// https://developer.themoviedb.org/reference/movie-top-rated-list
	public function getMovieTopRated(?string $region = null, ?string $language = null, ?int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'region' => (string) $region,
			'language' => ($language!== null) ? $language : $this->getLanguage()
		];

		return $this->makeCall('movie/now_playing', $params);
	}

	// https://developer.themoviedb.org/reference/movie-upcoming-list
	public function getMovieUpcoming(?string $region = null, ?string $language = null, ?int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'region' => (string) $region,
			'language' => ($language!== null) ? $language : $this->getLanguage()
		];

		return $this->makeCall('movie/now_playing', $params);
	}

	/**
	 * Movies
	 */

	// https://developer.themoviedb.org/reference/movie-details
	public function getMovie(int $movie_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('movie/'.$movie_id, $params);
	}

	// https://developer.themoviedb.org/reference/movie-alternative-titles
	public function getMovieAlternativeTitles(int $movie_id, ?string $country = null)
	{
		$params = [
			'country' => (string) $country
		];

		return $this->makeCall('movie/'.$movie_id.'/alternative_titles', $params);
	}

	// https://developer.themoviedb.org/reference/movie-changes
	public function getMovieChanges(int $movie_id, ?string $end_date = null, ?string $start_date = null, int $page = 1)
	{
		$params = [
			'end_date' => $end_date,
			'page' => (int) $page,
			'start_date' => $start_date
		];

		return $this->makeCall('movie/'.$movie_id.'/changes', $params);
	}

	// https://developer.themoviedb.org/reference/movie-credits
	public function getMovieCredits(int $movie_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('movie/'.$movie_id.'/credits', $params);
	}

	// https://developer.themoviedb.org/reference/movie-external-ids
	public function getMovieExternalIds(int $movie_id)
	{
		return $this->makeCall('movie/'.$movie_id.'/external_ids');
	}

	// https://developer.themoviedb.org/reference/movie-images

	public function getMovieImages(int $movie_id, ?string $include_image_language = null, ?string $language = null)
	{
		$params = [
			'include_image_language' => $include_image_language ?? $this->getLanguageISO(),
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('movie/'.$movie_id.'/images', $params);
	}

	// https://developer.themoviedb.org/reference/movie-keywords
	public function getMovieKeywords(int $movie_id)
	{
		return $this->makeCall('movie/'.$movie_id.'/keywords');
	}

	// https://developer.themoviedb.org/reference/movie-latest-id
	public function getMovieLatest()
	{
		return $this->makeCall('movie/latest');
	}

	// https://developer.themoviedb.org/reference/movie-lists
	public function getMovieLists(int $movie_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('movie/'.$movie_id.'/lists', $params);
	}

	// https://developer.themoviedb.org/reference/movie-recommendations
	public function getMovieRecommendations(int $movie_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('movie/'.$movie_id.'/recommendations', $params);
	}

	// https://developer.themoviedb.org/reference/movie-release-dates
	public function getMovieReleaseDates(int $movie_id)
	{
		return $this->makeCall('movie/'.$movie_id.'/release_dates');
	}

	// https://developer.themoviedb.org/reference/movie-reviews
	public function getMovieReviews(int $movie_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('movie/'.$movie_id.'/reviews', $params);
	}

	// https://developer.themoviedb.org/reference/movie-similar
	public function getMovieSimilar(int $movie_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('movie/'.$movie_id.'/similar', $params);
	}

	// https://developer.themoviedb.org/reference/movie-translations
	public function getMovieTranslations(int $movie_id)
	{
		return $this->makeCall('movie/'.$movie_id.'/translations');
	}


	// https://developer.themoviedb.org/reference/movie-videos
	public function getMovieVideos(int $movie_id, ?string $language = null)
	{
		// $params = [
		// 	'language' => $language ?? $this->getLanguage(),
		// ];

		$videosEn = $this->makeCall('movie/'.$movie_id.'/videos?language=en-US');
		$videosFr = $this->makeCall('movie/'.$movie_id.'/videos?language=fr-FR');

		$videosToutes = array_merge($videosFr->results, $videosEn->results);

		return $videosToutes;
		// return $this->makeCall('movie/'.$movie_id.'/videos', $params);
	}

	// https://developer.themoviedb.org/reference/movie-watch-providers
	public function getMovieWatchProviders(int $movie_id)
	{
		return $this->makeCall('movie/'.$movie_id.'/watch/providers');
	}

	/**
	 * Networks
	 */

	// https://developer.themoviedb.org/reference/network-details
	public function getNetwork(int $network_id)
	{
		return $this->makeCall('network/'.$network_id);
	}

	// https://developer.themoviedb.org/reference/details-copy
	public function getNetworkAlternativeNames(int $network_id)
	{
		return $this->makeCall('network/'.$network_id.'/alternative_names');
	}

	// https://developer.themoviedb.org/reference/alternative-names-copy
	public function getNetworkImages(int $network_id)
	{
		return $this->makeCall('network/'.$network_id.'/images');
	}

	/**
	 * People Lists
	 */

	// https://developer.themoviedb.org/reference/person-popular-list
	public function getPeoplePopular()
	{
		return $this->makeCall('person/popular');
	}

	/**
	 * People
	 */

	// https://developer.themoviedb.org/reference/person-details
	public function getPerson(int $person_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('person/'.$person_id, $params);
	}

	// https://developer.themoviedb.org/reference/person-changes
	public function getPersonChanges(int $person_id, ?string $end_date = null, int $page = 1, ?string $start_date = null)
	{
		$params = [
			'end_date' => $end_date,
			'page' => (int) $page,
			'start_date' => $start_date
		];

		return $this->makeCall('person/'.$person_id.'/changes', $params);
	}

	// https://developer.themoviedb.org/reference/person-combined-credits
	public function getPersonCombinedCredits(int $person_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('person/'.$person_id.'/combined_credits', $params);
	}

	// https://developer.themoviedb.org/reference/person-external-ids
	public function getPersonExternalIds(int $person_id)
	{
		return $this->makeCall('person/'.$person_id.'/external_ids');
	}

	// https://developer.themoviedb.org/reference/person-images
	public function getPersonImages(int $person_id)
	{
		return $this->makeCall('person/'.$person_id.'/images');
	}

	// https://developer.themoviedb.org/reference/person-latest-id
	public function getPersonLatest(int $person_id)
	{
		return $this->makeCall('person/latest');
	}

	// https://developer.themoviedb.org/reference/person-movie-credits
	public function getPersonMovieCredits(int $person_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('person/'.$person_id.'/movie_credits', $params);
	}

	// https://developer.themoviedb.org/reference/person-tv-credits
	public function getPersonTvCredits(int $person_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('person/'.$person_id.'/tv_credits', $params);
	}

	// https://developer.themoviedb.org/reference/translations
	public function getPersonTvTranslations(int $person_id)
	{
		return $this->makeCall('person/'.$person_id.'/translations');
	}

	/**
	 * Search
	 */

	// https://developer.themoviedb.org/reference/search-collection
	public function searchCollection(string $query, bool $include_adult = false, ?string $language = null, int $page = 1, ?string $region = null)
	{
		$params = [
			'query' => (string) $query,
			'include_adult' => (bool) $include_adult,
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page,
			'region' => (int) $region
		];

		return $this->makeCall('search/collection', $params);
	}

	// https://developer.themoviedb.org/reference/search-company
	public function searchCompany(string $query, int $page = 1)
	{
		$params = [
			'query' => (string) $query,
			'page' => (int) $page
		];

		return $this->makeCall('search/company', $params);
	}

	// https://developer.themoviedb.org/reference/search-keyword
	public function searchKeyword(string $query, int $page = 1)
	{
		$params = [
			'query' => (string) $query,
			'page' => (int) $page
		];

		return $this->makeCall('search/keyword', $params);
	}

	// https://developer.themoviedb.org/reference/search-movie
	public function searchMovie(string $query, bool $include_adult = false, ?string $language = null, ?int $primary_release_year = null, int $page = 1, ?string $region = null, ?int $year = null)
	{
		$params = [
			'query' => (string) $query,
			'include_adult' => (bool) $include_adult,
			'language' => $language ?? $this->getLanguage(),
			'primary_release_year' => (string) $primary_release_year,
			'page' => (int) $page,
			'region' => (string) $region,
			'year' => (string) $year,
		];

		return $this->makeCall('search/movie', $params);
	}
	// https://developer.themoviedb.org/reference/search-multi
	public function search(string $query, bool $include_adult = false, ?string $language = null, int $page = 1)
	{
		$params = [
			'query' => (string) $query,
			'include_adult' => (bool) $include_adult,
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('search/multi', $params);
	}

	// https://developer.themoviedb.org/reference/search-person
	public function searchPerson(string $query, bool $include_adult = false, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'query' => (string) $query,
			'page' => (int) $page,
			'include_adult' => (bool) $include_adult
		];

		return $this->makeCall('search/person', $params);
	}

	// https://developer.themoviedb.org/reference/search-tv
	public function searchTv(string $query, ?int $first_air_date_year = null, bool $include_adult = false, ?string $language = null, int $page = 1, ?int $year = null)
	{
		$params = [
			'query' => (string) $query,
			'first_air_date_year' => (int) $first_air_date_year,
			'include_adult' => (bool) $include_adult,
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page,
			'year' => (int) $year
		];

		return $this->makeCall('search/tv', $params);
	}

	/**
	 * Trending
	 */

	// https://developer.themoviedb.org/reference/trending-all
	public function getTrending(string $time_window = 'week', ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		$time_window = in_array($time_window ?? '', ['day', 'week']) ? $time_window : 'week';

		return $this->makeCall('trending/all/'.$time_window, $params);
	}

	// https://developer.themoviedb.org/reference/trending-movies
	public function getTrendingMovie(string $time_window = 'week', ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		$time_window = in_array($time_window ?? '', ['day', 'week']) ? $time_window : 'week';

		return $this->makeCall('trending/movie/'.$time_window, $params);
	}

	// https://developer.themoviedb.org/reference/trending-tv
	public function getTrendingTv(string $time_window = 'week', ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		$time_window = in_array($time_window ?? '', ['day', 'week']) ? $time_window : 'week';

		return $this->makeCall('trending/tv/'.$time_window, $params);
	}

	/**
	 * Tv Series Lists
	 */

	// https://developer.themoviedb.org/reference/tv-series-airing-today-list
	public function getTvAiringToday(int $page = 1, ?string $language = null, ?string $timezone = null)
	{
		$params = [
			'page' => (int) $page,
			'language' => $language ?? $this->getLanguage(),
			'timezone' => (string) $timezone
		];

		return $this->makeCall('tv/airing_today', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-on-the-air-list
	public function getTvOnTheAir(int $page = 1, ?string $language = null, ?string $timezone = null)
	{
		$params = [
			'page' => (int) $page,
			'language' => $language ?? $this->getLanguage(),
			'timezone' => (string) $timezone
		];

		return $this->makeCall('tv/on_the_air', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-popular-list
	public function getTvPopular(?string $language = null, int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/popular', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-top-rated-list
	public function getTvTopRated(?string $language = null, int $page = 1)
	{
		$params = [
			'page' => (int) $page,
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/top_rated', $params);
	}

	/**
	 * Tv Series
	 */

	// https://developer.themoviedb.org/reference/tv-series-details
	public function getTv(int $tv_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id, $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-aggregate-credits
	public function getTvAggregateCredits(int $tv_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/aggregate_credits', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-alternative-titles
	public function getTvAlternativeTitles(int $tv_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/alternative_titles', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-changes
	public function getTvChanges(int $tv_id, ?string $end_date = null, int $page = 1, ?string $start_date = null)
	{
		$params = [
			'start_date' => (string) $start_date,
			'page' => (int) $page,
			'end_date' => (string) $end_date
		];

		return $this->makeCall('tv/'.$tv_id.'/changes', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-content-ratings
	public function getTvContentRatings(int $tv_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/content_ratings', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-credits
	public function getTvCredits(int $tv_id, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/credits', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-episode-groups
	public function getTvEpisodeGroups(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/episode_groups');
	}

	// https://developer.themoviedb.org/reference/tv-series-external-ids
	public function getTvExternalIds(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/external_ids');
	}

	// https://developer.themoviedb.org/reference/tv-series-images
	public function getTvImages(int $tv_id, ?string $include_image_language = null, ?string $language = null)
	{
		$params = [
			'include_image_language' => $include_image_language ?? $this->getLanguageISO(),
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/images', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-keywords
	public function getTvKeywords(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/keywords');
	}

	// https://developer.themoviedb.org/reference/tv-series-latest-id
	public function getTvLatest()
	{
		return $this->makeCall('tv/latest');
	}

	// https://developer.themoviedb.org/reference/lists-copy
	public function getTvLists(int $tv_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('movie/'.$tv_id.'/lists', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-recommendations
	public function getTvRecommendations(int $tv_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('tv/'.$tv_id.'/recommendations', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-reviews
	public function getTvReviews(int $tv_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('tv/'.$tv_id.'/reviews', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-screened-theatrically
	public function getTvScreenedTheatrically(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/screened_theatrically');
	}

	// https://developer.themoviedb.org/reference/tv-series-similar
	public function getTvSimilar(int $tv_id, ?string $language = null, int $page = 1)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'page' => (int) $page
		];

		return $this->makeCall('tv/'.$tv_id.'/similar', $params);
	}

	// https://developer.themoviedb.org/reference/tv-series-translations
	public function getTvTranslations(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/translations');
	}

	// https://developer.themoviedb.org/reference/tv-series-videos
	public function getTvVideos(int $tv_id, ?string $include_video_language = null, ?string $language = null)
	{
		$videosEn = $this->makeCall('tv/'.$tv_id.'/videos?language=en-US');
		$videosFr = $this->makeCall('tv/'.$tv_id.'/videos?language=fr-FR');

		$videosToutes = array_merge($videosFr->results, $videosEn->results);

		return $videosToutes;
	}
	// 	$params = [
	// 		'include_video_language' => $include_video_language ?? $this->getLanguageISO(),
	// 		'language' => $language ?? $this->getLanguage(),
	// 	];

	// 	return $this->makeCall('tv/'.$tv_id.'/videos', $params);
	// }

	// https://developer.themoviedb.org/reference/tv-series-watch-providers
	public function getTvWatchProviders(int $tv_id)
	{
		return $this->makeCall('tv/'.$tv_id.'/watch/providers');
	}

	/**
	 * TV Seasons
	 */

	// https://developer.themoviedb.org/reference/tv-season-details
	public function getTvSeasons(?int $tv_id, ?int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number, $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-aggregate-credits
	public function getTvSeasonsAggregateCredits(int $tv_id, int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/aggregate_credits', $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-changes-by-id
	public function getTvSeasonsChanges(int $tv_id, ?string $end_date = null, int $page = 1, ?string $start_date = null)
	{
		$params = [
			'end_date' => (string) $end_date,
			'page' => (int) $page,
			'start_date' => (string) $start_date
		];

		return $this->makeCall('tv/season/'.$tv_id.'/changes', $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-credits
	public function getTvSeasonsCredits(int $tv_id, int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/credits', $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-external-ids
	public function getTvSeasonsExternalIds(int $tv_id, int $season_number)
	{
		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/external_ids');
	}

	// https://developer.themoviedb.org/reference/tv-season-images
	public function getTvSeasonsImages(int $tv_id, int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/images', $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-translations
	public function getTvSeasonsTranslations(int $tv_id, int $season_number)
	{
		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/translations');
	}

	// https://developer.themoviedb.org/reference/tv-season-videos
	public function getTvSeasonsVideos(int $tv_id, int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/videos', $params);
	}

	// https://developer.themoviedb.org/reference/tv-season-watch-providers
	public function getTvSeasonsWatchProviders(int $tv_id, int $season_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/watch/providers', $params);
	}

	/**
	 * Tv Episodes
	 */

	// https://developer.themoviedb.org/reference/tv-episode-details
	public function getTvEpisodes(int $tv_id, int $season_number, int $episode_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number, $params);
	}

	// https://developer.themoviedb.org/reference/tv-episode-changes-by-id
	public function getTvEpisodesChanges(int $episode_id)
	{
		return $this->makeCall('tv/episode/'.$episode_id.'/changes');
	}

	// https://developer.themoviedb.org/reference/tv-episode-credits
	public function getTvEpisodesCredits(int $tv_id, int $season_number, int $episode_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number.'/credits', $params);
	}

	// https://developer.themoviedb.org/reference/tv-episode-external-ids
	public function getTvEpisodesExternalIds(int $tv_id, int $season_number, int $episode_number)
	{
		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number.'/external_ids');
	}

	// https://developer.themoviedb.org/reference/tv-episode-images
	public function getTvEpisodesImages(int $tv_id, int $season_number, int $episode_number)
	{
		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number.'/images');
	}

	// https://developer.themoviedb.org/reference/tv-episode-translations
	public function getTvEpisodesTranslations(int $tv_id, int $season_number, int $episode_number)
	{
		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number.'/translations');
	}

	// https://developer.themoviedb.org/reference/tv-episode-videos
	public function getTvEpisodesVideos(int $tv_id, int $season_number, int $episode_number, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('tv/'.$tv_id.'/season/'.$season_number.'/episode/'.$episode_number.'/videos', $params);
	}

	/**
	 * Tv Episode Groups
	 */

	// https://developer.themoviedb.org/reference/tv-episode-group-details
	public function getTvEpisodeGroupsDetails(int $tv_episode_group_id)
	{
		return $this->makeCall('tv/episode_group/'.$tv_episode_group_id);
	}

	/**
	 * Watch Providers
	 */

	// https://developer.themoviedb.org/reference/watch-providers-available-regions
	public function getAvailableRegions(?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage()
		];

		return $this->makeCall('watch/providers/regions', $params);
	}

	// https://developer.themoviedb.org/reference/watch-providers-movie-list
	public function getMovieProviders(?string $watch_region, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'watch_region' => (string) $watch_region
		];

		return $this->makeCall('watch/providers/regions', $params);
	}

	// https://developer.themoviedb.org/reference/watch-provider-tv-list
	public function getTvProviders(?string $watch_region, ?string $language = null)
	{
		$params = [
			'language' => $language ?? $this->getLanguage(),
			'watch_region' => (string) $watch_region
		];

		return $this->makeCall('watch/providers/regions', $params);
	}

	public function getConfiguration()
	{
		$config = $this->makeCall('configuration');

		if(!empty($config))
			$this->setConfig($config);

		return $config;
	}

	public function getImageUrl(?string $cheminFichier, string $typeImage, string $taille, ?string $texte = null)
	{
		$config = $this->getConfig();

		if(!empty($cheminFichier))
		{
			if(isset($config->images))
			{
				if($taille == 'w90_and_h90_face')
					return $config->images->secure_base_url.'w90_and_h90_face'.$cheminFichier;

				else
				{
					$available_sizes = $this->getAvailableImageSizes($typeImage);

					return in_array($taille, $available_sizes) ? $config->images->secure_base_url.$taille.$cheminFichier : throw new TMDBException('The size "'.$taille.'" is not supported by TMDB');
				}
			}

			else
				goto end;

			// return
				// throw new TMDBException('No configuration available for image URL generation');
		}

		else
		{
			// Audiobookshelf
			// https://dummyimage.com/180x285/cccccc/555555.png?text=Stéphane+Rossignol

			end:
				// $tailles = [
				// 	'w92' => '92x138',
				// 	'w154' => '154x231',
				// 	'w185' => '185x277',
				// 	'w342' => '342x513',
				// 	'w500' => '500x750',
				// 	'w780' => '780x1170',
				// 	'w1280' => '850x1280',
				// 	'w90_and_h90_face' => '90x90',
				// 	'original' => 'original'
				// ];
				$tailles = [
					// backdrop_sizes
					// 'w300' => '300x170',
					// 'w780' => '780x440',
					'w1280' => '1280x720',

					// logo_sizes
					// 'w45' => '45x10',
					// 'w92' => '92x25',
					// 'w154' => '154x40',
					// 'w185' => '185x45',
					// 'w300' => '300x75',
					// 'w500' => '500x125',

					// poster_sizes
					// 'w92' => '92x140',
					// 'w154' => '154x230',
					'w185' => '185x280',
					// 'w342' => '342x515',
					// 'w500' => '500x750',
					'w780' => '780x1170',

					// profile_sizes
					'w45' => '45x70',
					'w185' => '185x280',
					'h632' => '420x632',
					'w90_and_h90_face' => '90x90',

					'original' => '1280x720',
				];

				$dimensions = $tailles[$taille] ?? $tailles['w1280'];
				$imageUrl = 'https://dummyimage.com/'.$dimensions.'/cccccc/555555.png';

				return !empty($texte) ? $imageUrl.'?text='.urlencode($texte) : $imageUrl;
		}
	}

	private function getAvailableImageSizes(string $typeImage)
	{
		$config = $this->getConfig();

		if(isset($config->images->{$typeImage.'_sizes'}))
			return $config->images->{$typeImage.'_sizes'};

		return
			throw new TMDBException('No configuration available to retrieve available image sizes');
	}

	private function makeCall(string $requete, ?array $params = null)
	{
		$params = !is_array($params) ? [] : $params;
		$params = array_filter($params);

		// f090bb54758cabf231fb605d3e3e0468		https://www.themoviedb.org/settings/api
		// 4e4ee9ed3ef0bb4d7fd0e5ee8bfe1603		https://github.com/xbmc/metadata.themoviedb.org.python/blob/master/python/lib/tmdbscraper/tmdbapi.py#L35

		$url = 'https://api.themoviedb.org/3/'.$requete.'?'.http_build_query($params);

		if(isset($params['language']) AND $params['language'] === false)
			unset($params['language']);

		try {
			$client = HttpClient::create([
				'headers' => [
					'Authorization'		=> 'Bearer eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI0ZTRlZTllZDNlZjBiYjRkN2ZkMGU1ZWU4YmZlMTYwMyIsIm5iZiI6MTU3ODM5NTc0OS4zOTIsInN1YiI6IjVlMTQ2ODY1NjI2MjA4MDAxNGM1NTBmMSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.Du9eqWLCBqhv-J38TAzkfoxDn-dpP5mrkvszs6djP8o',
					'Accept'			=> 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
					'Accept-Language'	=> 'fr-FR',
					'Cache-Control'		=> 'no-cache',
					'User-Agent'		=> 'TMDB API Explorer by Tulsow',
				]
			]);

			$response = $client->request('GET', $url);

			if($response->getStatusCode() === 200) {
				return json_decode($response->getContent());
			}
		} catch (\Exception $e) {
			echo alerte('danger', 'Erreur lors de la récupération d’une adresse <span class="fw-bold">TMDB</span> : '.$e->getMessage());
			// throw new Exception('Erreur TMDB :'.$e->getMessage());
		}

		return null;
	}

	public function setLanguage($language)
	{
		$this->language = $language;
	}

	public function getLanguage()
	{
		return $this->language;
	}

	public function setLanguageISO($languageISO)
	{
		$this->languageISO = $languageISO;
	}

	public function getLanguageISO()
	{
		return $this->languageISO;
	}

	public function setConfig($config)
	{
		$this->config = $config;
	}

	public function getConfig()
	{
		if(empty($this->config))
			$this->config = $this->getConfiguration();

		return $this->config;
	}
}

class TMDBException extends Exception { }