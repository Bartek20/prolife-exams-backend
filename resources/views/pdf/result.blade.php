@php
use \App\Utils\PDF\helpers\HelpersResult;
@endphp
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PROLIFE | Wyniki egzaminu</title>
    <style>
        .card {
            background-color: white;
            border-radius: 6px;
            margin-top: 24px;
            padding: 16px 24px 24px;
            width: 100%;
            border: 1px solid rgba(0, 0, 0, .25);
            margin-inline: auto;
            page-break-inside: avoid;
        }
        .results-header__h2 {
            font-size: 19px;
            margin: 0;
        }
        .results-header__p {
            font-size: 16px;
            margin: 0;
        }

        .option {
            border: 1px solid #e9ecef;
            background-color: #fcfcfd;
            color: #495057;
            padding: 12px 6px;
            border-radius: 6px;
            margin-top: 12px;
        }
        .question-invalid {
            background-color: #f8d7da;
            border-color: #f1aeb5;
            color: #58151c;
        }
        .question-valid {
            background-color: #d1e7dd;
            border-color: #a3cfbb;
            color: #0a3622;
        }
        .first {
            margin-top: 0;
        }
    </style>
</head>
<body>
<bookmark content="Wyniki egzaminu" level="0" />
<div class="card" style="padding-top: 24px">
    <table width="100%">
        <tr>
            <td class="results-header">
                <table>
                    <tr><td style="padding-bottom: 5px"><h2 class="results-header__h2">Zdający</h2></td></tr>
                    <tr><td style="padding-top: 5px"><p class="results-header__p">{{ $student['name'] }} {{ $student['surname'] }}</p></td></tr>
                    <tr><td style="padding-top: 5px"><p class="results-header__p" style="color: #8C8C8C">{{ $student['email'] }}</p></td></tr>
                    <tr><td style="padding-bottom: 5px"><h2 class="results-header__h2">Czas</h2></td></tr>
                    <tr><td style="padding-top: 5px"><p class="results-header__p"><b>Początek:</b> {{ $exam['start_time'] }}</p></td></tr>
                    <tr><td style="padding-top: 5px"><p class="results-header__p"><b>Koniec:</b> {{ $exam['end_time'] }}</p></td></tr>
                </table>
            </td>
            <td style="text-align: center" width="150px">
                @php
                    $graph = HelpersResult::getGraphConfig($score);
                @endphp
                <svg viewBox="0 0 150 150">
                    <g fill="transparent">
                        <circle cx="75" cy="75" r="70" stroke-width="2" stroke="#404040"></circle>
                        <circle cx="75" cy="75" r="70" stroke-width="4" stroke="{{ $graph['color'] }}" stroke-dasharray="440" stroke-dashoffset="{{ $graph['offset'] }}"></circle>
                    </g>

                    <circle cx="{{ $graph['dot']['x'] }}" cy="{{ $graph['dot']['y'] }}" r="5" fill="{{ $graph['color'] }}" style="filter: drop-shadow(3px 5px 2px {{ $graph['color'] }})"></circle>

                    <g font-family="montserrat" font-weight="bold" text-anchor="middle">
                        <text x="75" y="70" font-size="24">{{ number_format($score['exam'] / $score['max'] * 100, 2, thousands_separator: '') }}%</text>
                        <text x="75" y="93" font-size="18" fill="#7F7F7F">{{ $score['exam'] }}/{{ $score['max'] }}</text>
                    </g>
                </svg>
                <br>
                <p style="font-size: 18px; color: {{ $graph['color'] }}"><b>{{ $graph['text'] }}</b></p>
            </td>
        </tr>
    </table>
</div>
@foreach($questions as $idx => $question)
    <div class="card">
        <bookmark content="Pytanie {{ $idx + 1 }}" level="0" />
        <table width="100%">
            <tr>
                @php
                $points = HelpersResult::getQuestionPoints($question);
                @endphp
                <td style="padding-bottom: 8px"><h2 style="margin: 0; padding: 0">Pytanie {{ $idx + 1 }}</h2></td>
                <td style="padding-bottom: 8px" align="right"><h2 style="margin: 0; padding: 0; color: {{ $points['color'] }}">{{ $points['points'] }}/1</h2></td>
            </tr>
            <tr>
                <td colspan="2" style="padding-bottom: 4px">
                    <h3 style="margin: 0; padding: 0">{{ $question['question'] }}</h3>
                    @if(array_key_exists('question', $question['images']))
                        <br />
                        <img src="var:{{ $question['images']['question'] }}" alt="Zdjęcie do pytania" style="max-width: 300px; max-height: 250px; margin-top: -8px; border: 1px solid rgba(0,0,0,0.25); border-radius: 6px" />
                    @endif
                </td>
            </tr>
        </table>
        <div>
            @foreach($question['options'] as $i => $option)
                <div @class([
                        'option',
                        'first' => $i == 0,
                        'question-valid' => $i == $question['correct'],
                        'question-invalid' => $i == $question['answer'] && $i != $question['correct']
                    ])>
                    <table>
                        <tr>
                            <td width="16px">
                                <svg viewBox="0 0 16 16">
                                    @if($i == $question['answer'])
                                        <circle cx="8" cy="8" r="5" fill="#fcfcfd" stroke="#0d6efd" stroke-width="3"/>
                                    @else
                                        <circle cx="8" cy="8" r="6" fill="#fcfcfd" stroke="#e9ecef" stroke-width="2"/>
                                    @endif
                                </svg>
                            </td>
                            <td>
                                <b>{{ HelpersResult::numberToLetter($i + 1) }}.</b> {{ json_decode('"' . $option . '"') }}
                            </td>
                        </tr>
                    </table>
                </div>
            @endforeach
        </div>
    </div>
@endforeach
</body>
</html>
