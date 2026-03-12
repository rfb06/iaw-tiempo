<?php
require_once __DIR__ . '/../models/WeatherModel.php';

class WeatherController
{
    private WeatherModel $model;
    private const HISTORY_MAX = 5;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new WeatherModel();
    }

    public function index(): void
    {
        $this->render('home', [
            'title'   => 'Weather App',
            'history' => $this->getHistory(),
        ]);
    }

    public function search(): void
    {
        $city = trim($_POST['city'] ?? '');

        if ($city === '') {
            $this->render('home', [
                'title'   => 'Weather App',
                'error'   => 'Por favor, introduce el nombre de una ciudad.',
                'history' => $this->getHistory(),
            ]);
            return;
        }

        try {
            $geo = $this->model->geocode($city);
            $this->addHistory($geo);
            $qs = http_build_query([
                'name'    => $geo['name'],
                'country' => $geo['country'],
                'state'   => $geo['state'],
                'lat'     => $geo['lat'],
                'lon'     => $geo['lon'],
            ]);
            header('Location: ' . APP_BASE . '/city?' . $qs);
            exit;

        } catch (RuntimeException $e) {
            $this->render('home', [
                'title'     => 'Weather App',
                'error'     => $e->getMessage(),
                'cityInput' => htmlspecialchars($city),
                'history'   => $this->getHistory(),
            ]);
        }
    }

    public function city(): void
    {
        $geo = $this->geoFromQuery();
        if (!$geo) { $this->redirectHome(); return; }

        try {
            $weather = $this->model->getCurrent($geo['lat'], $geo['lon']);
            $hours   = $this->model->getHourly($geo['lat'], $geo['lon']);
            $days    = $this->model->getWeekly($geo['lat'], $geo['lon']);
        } catch (RuntimeException $e) {
            $weather = null;
            $hours   = [];
            $days    = [];
            $apiError = $e->getMessage();
        }

        $this->render('city', [
            'title'    => $geo['name'] . ', ' . $geo['country'],
            'geo'      => $geo,
            'weather'  => $weather ?? null,
            'hours'    => $hours ?? [],
            'days'     => $days ?? [],
            'apiError' => $apiError ?? null,
        ]);
    }

    public function current(): void
    {
        $geo = $this->geoFromQuery();
        if (!$geo) { $this->redirectHome(); return; }
        try {
            $weather = $this->model->getCurrent($geo['lat'], $geo['lon']);
            $this->render('current', [
                'title'   => 'Tiempo actual · ' . $geo['name'],
                'geo'     => $geo,
                'weather' => $weather,
            ]);
        } catch (RuntimeException $e) {
            $this->renderError($e->getMessage(), $geo);
        }
    }

    public function hourly(): void
    {
        $geo = $this->geoFromQuery();
        if (!$geo) { $this->redirectHome(); return; }
        try {
            $hours = $this->model->getHourly($geo['lat'], $geo['lon']);
            $this->render('hourly', [
                'title' => 'Previsión por horas · ' . $geo['name'],
                'geo'   => $geo,
                'hours' => $hours,
            ]);
        } catch (RuntimeException $e) {
            $this->renderError($e->getMessage(), $geo);
        }
    }

    public function weekly(): void
    {
        $geo = $this->geoFromQuery();
        if (!$geo) { $this->redirectHome(); return; }
        try {
            $days = $this->model->getWeekly($geo['lat'], $geo['lon']);
            $this->render('weekly', [
                'title' => 'Previsión semanal · ' . $geo['name'],
                'geo'   => $geo,
                'days'  => $days,
            ]);
        } catch (RuntimeException $e) {
            $this->renderError($e->getMessage(), $geo);
        }
    }

    private function addHistory(array $geo): void
    {
        $history = $_SESSION['search_history'] ?? [];
        $history = array_values(array_filter(
            $history,
            fn($h) => strtolower($h['name']) !== strtolower($geo['name'])
        ));
        array_unshift($history, [
            'name'    => $geo['name'],
            'country' => $geo['country'],
            'state'   => $geo['state'],
            'lat'     => $geo['lat'],
            'lon'     => $geo['lon'],
        ]);
        $_SESSION['search_history'] = array_slice($history, 0, self::HISTORY_MAX);
    }

    private function getHistory(): array
    {
        return $_SESSION['search_history'] ?? [];
    }

    private function geoFromQuery(): ?array
    {
        $lat = filter_input(INPUT_GET, 'lat', FILTER_VALIDATE_FLOAT);
        $lon = filter_input(INPUT_GET, 'lon', FILTER_VALIDATE_FLOAT);
        if ($lat === false || $lon === false || $lat === null || $lon === null) {
            return null;
        }
        return [
            'name'    => htmlspecialchars($_GET['name']    ?? ''),
            'country' => htmlspecialchars($_GET['country'] ?? ''),
            'state'   => htmlspecialchars($_GET['state']   ?? ''),
            'lat'     => $lat,
            'lon'     => $lon,
        ];
    }

    public static function geoQs(array $geo): string
    {
        return http_build_query([
            'name'    => $geo['name'],
            'country' => $geo['country'],
            'state'   => $geo['state'],
            'lat'     => $geo['lat'],
            'lon'     => $geo['lon'],
        ]);
    }

    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../views/layout/header.php';
        require __DIR__ . "/../views/{$view}.php";
        require __DIR__ . '/../views/layout/footer.php';
    }

    private function renderError(string $msg, array $geo): void
    {
        $this->render('city', [
            'title' => $geo['name'] . ', ' . $geo['country'],
            'geo'   => $geo,
            'error' => $msg,
        ]);
    }

    private function redirectHome(): void
    {
        header('Location: ' . APP_BASE . '/');
        exit;
    }
}
