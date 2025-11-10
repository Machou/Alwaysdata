<?php
class Paginator
{
	const PLACEHOLDER = '(:num)';

	protected int $elementsTotal;
	protected int $numPages;
	protected int $elementsParPage;
	protected int $pageActuelle;
	protected string $modeleUrl;
	protected int $maxPagesAfficher = 5;
	protected string $textePrecedent = '‹';
	protected string $texteSuivant = '›';

	public function __construct(int $elementsTotal, int $elementsParPage, int $pageActuelle, string $modeleUrl = '')
	{
		$this->elementsTotal = $elementsTotal;
		$this->elementsParPage = $elementsParPage;
		$this->pageActuelle = $pageActuelle;
		$this->modeleUrl = $modeleUrl;

		$this->updateNumPages();
	}

	protected function updateNumPages(): void
	{
		$this->numPages = ($this->elementsParPage > 0) ? (int) ceil($this->elementsTotal / $this->elementsParPage) : 0;
	}

	public function setMaxPagesAfficher(int $max): static
	{
		if($max < 3) {
			throw new \InvalidArgumentException('maxPagesAfficher ne peut être inférieur à 3');
		}

		$this->maxPagesAfficher = $max;

		return $this;
	}

	public function getMaxPagesAfficher(): int
	{
		return $this->maxPagesAfficher;
	}

	public function setPageActuelle(int $page): static
	{
		$this->pageActuelle = $page;

		return $this;
	}

	public function getPageActuelle(): int
	{
		return $this->pageActuelle;
	}

	public function setElementsParPage(int $valeur): static
	{
		$this->elementsParPage = $valeur;
		$this->updateNumPages();

		return $this;
	}

	public function getElementsParPage(): int
	{
		return $this->elementsParPage;
	}

	public function setElementsTotal(int $valeur): static
	{
		$this->elementsTotal = $valeur;
		$this->updateNumPages();

		return $this;
	}

	public function getElementsTotal(): int
	{
		return $this->elementsTotal;
	}

	public function getNumPages(): int
	{
		return $this->numPages;
	}

	public function setModeleUrl(string $url): static
	{
		$this->modeleUrl = $url;

		return $this;
	}

	public function getModeleUrl(): string
	{
		return $this->modeleUrl;
	}

	public function getPageUrl(int $num): string
	{
		return str_replace(self::PLACEHOLDER, $num, $this->modeleUrl);
	}

	public function getNextPage(): ?int
	{
		return $this->pageActuelle < $this->numPages ? $this->pageActuelle + 1 : null;
	}

	public function getPrevPage(): ?int
	{
		return $this->pageActuelle > 1 ? $this->pageActuelle - 1 : null;
	}

	public function getNextUrl(): ?string
	{
		$next = $this->getNextPage();

		return $next ? $this->getPageUrl($next) : null;
	}

	public function getPrevUrl(): ?string
	{
		$prev = $this->getPrevPage();

		return $prev ? $this->getPageUrl($prev) : null;
	}

	public function getPages(): array
	{
		$pages = [];

		if($this->numPages <= 1) return [];

		if($this->numPages <= $this->maxPagesAfficher)
		{
			for($i = 1; $i <= $this->numPages; $i++)
			{
				$pages[] = $this->createPage($i, $i === $this->pageActuelle);
			}

		}

		else
		{
			$adj = (int) floor(($this->maxPagesAfficher - 3) / 2);
			$start = max(2, min($this->pageActuelle - $adj, $this->numPages - $this->maxPagesAfficher + 2));
			$end = min($this->numPages - 1, $start + $this->maxPagesAfficher - 3);

			$pages[] = $this->createPage(1, $this->pageActuelle === 1);
			if($start > 2) $pages[] = $this->createEllipsis();

			for($i = $start; $i <= $end; $i++) {
				$pages[] = $this->createPage($i, $i === $this->pageActuelle);
			}

			if($end < $this->numPages - 1)
				$pages[] = $this->createEllipsis();

			$pages[] = $this->createPage($this->numPages, $this->pageActuelle === $this->numPages);
		}

		return $pages;
	}

	protected function createPage(int $num, bool $current = false): array
	{
		return [
			'num' => $num,
			'url' => $this->getPageUrl($num),
			'isCurrent' => $current,
		];
	}

	protected function createEllipsis(): array
	{
		return [
			'num' => '…',
			'url' => null,
			'isCurrent' => false,
		];
	}

	public function toHtml(): string
	{
		if($this->numPages <= 1)
			return '';

		$h = fn($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');

		$html = '<ul class="pagination justify-content-center m-0">';

		if($url = $this->getPrevUrl()) {
			$html .= '<li class="page-item"><a href="'.$h($url).'" class="page-link" title="Page précédente">'.$h($this->textePrecedent).'</a></li>';
		}

		$html .= '<li class="page-item"><a href="'.$h($this->getPageUrl(1)).'" class="page-link" title="Première page">«</a></li>';

		foreach($this->getPages() as $page) {
			if($page['url'])
				$html .= '<li class="page-item'.($page['isCurrent'] ? ' active" aria-current="page' : '').'"><a href="'.$h($page['url']).'" class="page-link" title="Page n°'.$h($page['num']).'">'.$h($page['num']).'</a></li>';

			else
				$html .= '<li class="page-item"><span class="page-link">…</span></li>';
		}

		$html .= '<li class="page-item"><a href="'.$h($this->getPageUrl($this->numPages)).'" class="page-link" title="Dernière page">»</a></li>';

		if($url = $this->getNextUrl())
			$html .= '<li class="page-item"><a href="'.$h($url).'" class="page-link" title="Page suivante">'.$h($this->texteSuivant).'</a></li>';

		$html .= '</ul>';

		return $html;
	}

	public function __toString(): string
	{
		return $this->toHtml();
	}

	public function getPremierElement(): ?int
	{
		$premier = ($this->pageActuelle - 1) * $this->elementsParPage + 1;

		return $premier > $this->elementsTotal ? null : $premier;
	}

	public function getDernierElement(): ?int
	{
		$premier = $this->getPremierElement();

		if($premier === null)
			return null;

		$last = $premier + $this->elementsParPage - 1;

		return min($last, $this->elementsTotal);
	}

	public function setTextePrecedent(string $text): static
	{
		$this->textePrecedent = $text;

		return $this;
	}

	public function setTexteSuivant(string $text): static
	{
		$this->texteSuivant = $text;

		return $this;
	}
}