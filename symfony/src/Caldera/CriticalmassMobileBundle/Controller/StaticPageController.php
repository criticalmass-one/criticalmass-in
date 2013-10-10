<?php

namespace Caldera\CriticalmassMobileBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Dieser Controller dient lediglich der Anzeige statischer Seiten, etwa dem
 * Impressum oder den Datenschutzrichtlinien. Aus Sicherheitsgruenden muessen
 * die anzeigbaren Seiten zunaechst aktiviert werden.
 */
class StaticPageController extends Controller
{
	/**
	 * Zeigt den Inhalt der angeforderten Seite an.
	 *
	 * @param String $page: BEzeichnung der Seite
	 */
	public function showAction($page)
	{
		// Liste der freigeschalteten Seiten
		$enabledTemplates = array('impress', 'privacy');

		// ist die angeforderte Seite freigeschaltet?
		if (in_array($page, $enabledTemplates))
		{
			// dann rendern
			return $this->render('CalderaCriticalmassMobileBundle:Static:'.$page.'.html.twig');
		}
		else
		{
			// ansonsten Fehlermeldung angeben.
			throw $this->createNotFoundException('Die Seite '.$page.' existiert nicht.');
		}
	}
}
