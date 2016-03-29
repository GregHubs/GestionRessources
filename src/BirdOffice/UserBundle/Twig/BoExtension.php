<?php

namespace BirdOffice\BirdOfficeBundle\Twig;

use Symfony\Bundle\FrameworkBundle\Translation\Translator;
use Symfony\Component\DependencyInjection\Container;

class BoExtension extends \Twig_Extension {
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var Container
     */
    private $container;

    /**
     * @param Translator $translator
     * @param Container $container
     * @param \Eko\GoogleTranslateBundle\Translate\Method\Translator $googleTranslate
     */
    public function __construct(Translator $translator, Container $container, \Eko\GoogleTranslateBundle\Translate\Method\Translator $googleTranslate)
    {
        $this->translator = $translator;
        $this->container = $container;
        $this->googleTranslate = $googleTranslate;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('crypt', array($this, 'getCryptId')),
            new \Twig_SimpleFilter('routeExists', array($this, 'routeExists')),
            new \Twig_SimpleFilter('googleTrans', array($this, 'googleTrans')),
        );
    }

    /**
     * Return the functions registered as twig extensions
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'file_exists' => new \Twig_SimpleFunction('file_exists', 'file_exists'),
        );
    }


    /* PLUS UTILISE (à partir du 03/12/2015 avec le calendar) */
    /**
     * @param $tabTimeSlot
     * @param $locale
     */
    /*
    public function getCalendar($tabTimeSlot, $locale)
    {
        $this->translator->setLocale($locale);
        $week = array(
                1 => $this->translator->trans('bo.global.days.monday.short'),
                2 => $this->translator->trans('bo.global.days.tuesday.short'),
                3 => $this->translator->trans('bo.global.days.wednesday.short'),
                4 => $this->translator->trans('bo.global.days.thursday.short'),
                5 => $this->translator->trans('bo.global.days.friday.short'),
                6 => $this->translator->trans('bo.global.days.saturday.short'),
                7 => $this->translator->trans('bo.global.days.sunday.short')
        );
        $separator = array(2.5, 5.5, 8.5, 11.5, 14.5, 17.5, 20.5);
        $scheduleTime = array(1 => array(),2 => array(),3 => array(),4 => array(),5 => array(),6 => array(),7 => array());

        foreach ($tabTimeSlot as $date) {
            $start = str_replace(':30', '.5', $date->getTimeSlot()->getStart()->format('G:i'));
            $start = str_replace(':00', '', $start);
            $end = str_replace(':30', '.5', $date->getTimeSlot()->getEnd()->format('G:i'));
            $end = str_replace(':00', '', $end);
            if ($end == 0) {
                $end = 24;
            }

            for ($i = (float)$start ; $i < (float)$end ; $i += 0.5) {
                array_push($scheduleTime[$date->getDay()->getId()], $i);
            }
        }

?>
        <div class="schedule">
            <table width="100%">
                <tr>
                    <th width="30px"></th>
                    <td>
                        <div class="caption">6h</div>
                        <div class="caption">9h</div>
                        <div class="caption">12h</div>
                        <div class="caption">15h</div>
                        <div class="caption">18h</div>
                        <div class="caption">21h</div>
                    </td>
                </tr>
                <?php

                    foreach($scheduleTime as $day => $daySlot) {
                        echo '<tr style="height:25px">';
                            echo '<th width="30px">'.$week[$day].'</th>';
                            echo '<td>';
                                echo '<div>';

                                    if (isset($scheduleTime[$day]) && !empty($scheduleTime[$day])){

                                        echo '<ol id="selectableJour'.$day.'" class="selectable schedule-tab" >';

                                            for ($i = 6 ; $i < 24 ; $i+=0.5){
                                                $margin_right = $selected = '';

                                                if (strpos($i, '.')) $margin_right = ' margin-right-1';
                                                if (in_array($i, $separator)) $margin_right = ' margin-right-5';
                                                if (in_array($i, $scheduleTime[$day])) $selected = ' ui-selected';

                                                $tooltip = $i.'h';
                                                if (strpos($i, '.')) {
                                                    $tooltip = str_replace('.5', 'h30', $i);
                                                }

                                                echo '<li value="'.$week[$day].'" class="ui-widget-content '.$margin_right.$selected.'" title="'.$tooltip.'" >&nbsp;</li>';
                                            }

                                        echo '</ol>';
                                    } else {
                                        echo $this->translator->trans('bo.global.closed');
                                    }
                                echo '</div>';
                            echo '</td>';
                        echo '</tr>';
                    }

                ?>
            </table>
        </div>
<?php
    }
*/

    /**
     * @param $id
     * @return string
     */
    public function getCryptId($id)
    {
        return '1'.((int)$id*3);
    }

    /**
     * @param $route
     * @return string
     */
    public function routeExists($route) {
        $router = $this->container->get('router');
        return (null === $router->getRouteCollection()->get($route)) ? "0" : "1";
    }

    /**
     * @param $text
     * @param $partnerLanguage
     * @return array|string
     */
    public function googleTrans($text, $partnerLanguage) {
        return $this->googleTranslate->translate($text, $partnerLanguage);
    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'bo_extension';
    }
} 