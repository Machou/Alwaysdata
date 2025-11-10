<?php
class IMDb
{
	private $id;

	public function __construct($id) {
		$this->id = $id;
	}

	public function grab()
	{
		try {
			$config = new \Imdb\Config();
			$config->language = 'fr, fr-FR, fr_FR, en, en-US, en_US';
			$config->cache_expire = 86400;
		} catch (\Exception $e) {
			throw new Exception('Erreur IMDb : '.$e->getMessage());
		}

		if(!empty($this->id)) {
			$id = str_replace('tt', '', $this->id);
			$imdb_fetch = new \Imdb\Title($id, $config);

			return $imdb_fetch;
		}

		return null;
	}

	private function getNote(): ?string
	{
		$imdb = $this->grab();

		return (!is_null($this->id) AND !is_null($imdb) AND !empty($imdb->rating())) ? $imdb->rating() : null;
	}

	public function note(): ?string
	{
		return $this->getNote() ? $this->getNote().' / 10' : null;
	}

	public function getNbVotes(): ?string
	{
		$imdb = $this->grab();

		return (!is_null($this->id) AND !is_null($imdb) AND $imdb->votes() > 0) ? $imdb->votes() : null;
	}

	public function nbVotes(): ?string
	{
		return $this->getNbVotes() ? number_format($this->getNbVotes(), 0, ' ', ' ').' votes' : null;
	}
}