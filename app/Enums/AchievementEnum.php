<?php

namespace App\Enums;

enum AchievementEnum: string
{
    case FirstLessonWatched = 'First Lesson Watched';
    case FiveLessonsWatched = '5 Lessons Watched';
    case TenLessonsWatched = '10 Lessons Watched';
    case TwentyFiveLessonsWatched = '25 Lessons Watched';
    case FiftyLessonsWatched = '50 Lessons Watched';

    case FirstCommentWritten = 'First Comment Written';
    case ThreeCommentsWritten = '3 Comments Written';
    case FiveCommentsWritten = '5 Comments Written';
    case TenCommentsWritten = '10 Comments Written';
    case TwentyCommentsWritten = '20 Comments Written';
}
