<?php

namespace App\Models;

class Jadwal extends Model
{
    protected $defaultOpenTime = '06:00';
    protected $defaultCloseTime = '23:00';

    public function ensureForField($fieldId, $startDate = null, $days = 30)
    {
        $field = $this->fieldRow($fieldId);

        if (!$field) {
            return 0;
        }

        $dates = array();
        $cursor = $this->parseDate($startDate ?: date('Y-m-d'));

        if (!$cursor) {
            $cursor = new \DateTimeImmutable(date('Y-m-d'));
        }

        $days = max(1, (int) $days);

        for ($index = 0; $index < $days; $index++) {
            $dates[] = $cursor->modify('+' . $index . ' days')->format('Y-m-d');
        }

        return $this->ensureFieldDates($field, $dates);
    }

    public function ensureForFieldDate($fieldId, $date)
    {
        $field = $this->fieldRow($fieldId);

        if (!$field || !$this->isValidDate($date) || $date < date('Y-m-d')) {
            return 0;
        }

        return $this->ensureFieldDates($field, array($date));
    }

    public function ensureForOwnerDate($ownerId, $date)
    {
        if (trim((string) $ownerId) === '' || !$this->isValidDate($date) || $date < date('Y-m-d')) {
            return 0;
        }

        $statement = mysqli_prepare(
            $this->db(),
            'SELECT ID_Lapangan, Harga FROM lapangan WHERE ID_Pemilik = ? AND deleted_at IS NULL'
        );

        if (!$statement) {
            return 0;
        }

        mysqli_stmt_bind_param($statement, 's', $ownerId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $created = 0;

        while ($result && $field = mysqli_fetch_assoc($result)) {
            $created += $this->ensureFieldDates($field, array($date));
        }

        mysqli_stmt_close($statement);

        return $created;
    }

    public function setOwnerSlotStatus($ownerId, $fieldId, $date, $startTime, $status)
    {
        $ownerId = trim((string) $ownerId);
        $fieldId = trim((string) $fieldId);
        $date = trim((string) $date);
        $startTime = $this->normalizeTime($startTime);
        $status = $this->normalizeSlotStatus($status);

        if ($ownerId === '' || $fieldId === '' || !$this->isValidDate($date) || $date < date('Y-m-d') || $startTime === '' || $status === '') {
            return array('ok' => false, 'message' => 'Data jadwal tidak valid.');
        }

        $field = $this->fieldRow($fieldId, $ownerId);

        if (!$field) {
            return array('ok' => false, 'message' => 'Lapangan tidak ditemukan.');
        }

        $this->ensureFieldDates($field, array($date));

        $statement = mysqli_prepare(
            $this->db(),
            "SELECT j.ID_Jadwal, j.Status,
                    (SELECT COUNT(*) FROM booking b
                     WHERE b.ID_Jadwal = j.ID_Jadwal
                       AND LOWER(TRIM(b.Status)) NOT IN ('dibatalkan', 'cancelled', 'batal')) AS active_bookings
             FROM jadwal j
             WHERE j.ID_Lapangan = ? AND j.Tanggal = ? AND j.Jam_Mulai = ?
             LIMIT 1"
        );

        if (!$statement) {
            return array('ok' => false, 'message' => 'Jadwal tidak dapat dibaca.');
        }

        mysqli_stmt_bind_param($statement, 'sss', $fieldId, $date, $startTime);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $slot = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        if (!$slot) {
            return array('ok' => false, 'message' => 'Slot berada di luar jam operasional.');
        }

        if ((int) $slot['active_bookings'] > 0) {
            return array('ok' => false, 'message' => 'Slot sudah dipesan customer.');
        }

        $update = mysqli_prepare($this->db(), 'UPDATE jadwal SET Status = ? WHERE ID_Jadwal = ?');

        if (!$update) {
            return array('ok' => false, 'message' => 'Status jadwal belum dapat disimpan.');
        }

        mysqli_stmt_bind_param($update, 'ss', $status, $slot['ID_Jadwal']);
        $saved = mysqli_stmt_execute($update);
        mysqli_stmt_close($update);

        if (!$saved) {
            return array('ok' => false, 'message' => 'Status jadwal belum dapat disimpan.');
        }

        return array(
            'ok' => true,
            'id' => $slot['ID_Jadwal'],
            'status' => $status,
            'state' => $this->slotState($status, 0),
            'message' => 'Status jadwal diperbarui.',
        );
    }

    public function slotMapForFieldDate($fieldId, $date)
    {
        $slots = array();

        if (!$this->isValidDate($date)) {
            return $slots;
        }

        $statement = mysqli_prepare(
            $this->db(),
            "SELECT j.Jam_Mulai, j.Status,
                    (SELECT COUNT(*) FROM booking b
                     WHERE b.ID_Jadwal = j.ID_Jadwal
                       AND LOWER(TRIM(b.Status)) NOT IN ('dibatalkan', 'cancelled', 'batal')) AS active_bookings
             FROM jadwal j
             WHERE j.ID_Lapangan = ? AND j.Tanggal = ?
             ORDER BY j.Jam_Mulai"
        );

        if (!$statement) {
            return $slots;
        }

        mysqli_stmt_bind_param($statement, 'ss', $fieldId, $date);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($result && $slot = mysqli_fetch_assoc($result)) {
            $time = isset($slot['Jam_Mulai']) ? substr((string) $slot['Jam_Mulai'], 0, 5) : '';

            if ($time !== '') {
                $slots[$time] = $this->slotState($slot['Status'], (int) $slot['active_bookings']);
            }
        }

        mysqli_stmt_close($statement);

        return $slots;
    }

    protected function ensureFieldDates(array $field, array $dates)
    {
        $fieldId = isset($field['ID_Lapangan']) ? trim((string) $field['ID_Lapangan']) : '';

        if ($fieldId === '') {
            return 0;
        }

        $hours = $this->operationalHours($fieldId);
        $statement = mysqli_prepare(
            $this->db(),
            "INSERT IGNORE INTO jadwal (ID_Jadwal, ID_Lapangan, Tanggal, Jam_Mulai, Jam_Selesai, Status, Harga)
             VALUES (?, ?, ?, ?, ?, 'Available', 0)"
        );

        if (!$statement) {
            return 0;
        }

        $created = 0;

        foreach ($dates as $date) {
            if (!$this->isValidDate($date) || $date < date('Y-m-d')) {
                continue;
            }

            $day = (int) date('w', strtotime($date));
            $dayHours = $this->hoursForDay($hours, $day);

            if ($dayHours['closed']) {
                continue;
            }

            foreach ($this->slotRanges($dayHours['open'], $dayHours['close']) as $range) {
                $id = $this->scheduleId($fieldId, $date, $range['start'], $range['end']);
                mysqli_stmt_bind_param($statement, 'sssss', $id, $fieldId, $date, $range['start'], $range['end']);
                mysqli_stmt_execute($statement);

                if (mysqli_stmt_affected_rows($statement) === 1) {
                    $created++;
                }
            }
        }

        mysqli_stmt_close($statement);

        return $created;
    }

    protected function fieldRow($fieldId, $ownerId = null)
    {
        $fieldId = trim((string) $fieldId);

        if ($fieldId === '') {
            return null;
        }

        if ($ownerId !== null) {
            $statement = mysqli_prepare(
                $this->db(),
                'SELECT ID_Lapangan, Harga FROM lapangan WHERE ID_Lapangan = ? AND ID_Pemilik = ? AND deleted_at IS NULL LIMIT 1'
            );

            if (!$statement) {
                return null;
            }

            mysqli_stmt_bind_param($statement, 'ss', $fieldId, $ownerId);
        } else {
            $statement = mysqli_prepare(
                $this->db(),
                'SELECT ID_Lapangan, Harga FROM lapangan WHERE ID_Lapangan = ? AND deleted_at IS NULL LIMIT 1'
            );

            if (!$statement) {
                return null;
            }

            mysqli_stmt_bind_param($statement, 's', $fieldId);
        }

        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $row ?: null;
    }

    protected function operationalHours($fieldId)
    {
        $rows = array();
        $statement = mysqli_prepare(
            $this->db(),
            'SELECT Hari, Jam_buka, Jam_tutup, Tutup FROM jam_operasional WHERE ID_Lapangan = ?'
        );

        if (!$statement) {
            return $rows;
        }

        mysqli_stmt_bind_param($statement, 's', $fieldId);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);

        while ($result && $row = mysqli_fetch_assoc($result)) {
            $rows[(int) $row['Hari']] = $row;
        }

        mysqli_stmt_close($statement);

        return $rows;
    }

    protected function hoursForDay(array $hours, $day)
    {
        if (!isset($hours[$day])) {
            return array('open' => $this->defaultOpenTime, 'close' => $this->defaultCloseTime, 'closed' => false);
        }

        $row = $hours[$day];

        if (!empty($row['Tutup'])) {
            return array('open' => $this->defaultOpenTime, 'close' => $this->defaultCloseTime, 'closed' => true);
        }

        $open = !empty($row['Jam_buka']) ? substr((string) $row['Jam_buka'], 0, 5) : $this->defaultOpenTime;
        $close = !empty($row['Jam_tutup']) ? substr((string) $row['Jam_tutup'], 0, 5) : $this->defaultCloseTime;

        return array('open' => $open, 'close' => $close, 'closed' => false);
    }

    protected function slotRanges($open, $close)
    {
        $ranges = array();
        $start = $this->timeToMinutes($open);
        $closeAt = $this->timeToMinutes($close);

        if ($start === null || $closeAt === null || $start >= $closeAt) {
            return $ranges;
        }

        while ($start + 60 <= $closeAt) {
            $end = $start + 60;
            $ranges[] = array(
                'start' => $this->minutesToTime($start),
                'end' => $this->minutesToTime($end),
            );
            $start = $end;
        }

        return $ranges;
    }

    protected function normalizeSlotStatus($status)
    {
        $status = strtolower(trim((string) $status));

        if (in_array($status, array('available', 'tersedia', 'aktif'), true)) {
            return 'Available';
        }

        if (in_array($status, array('blocked', 'unavailable', 'tidak tersedia', 'maintenance', 'nonaktif'), true)) {
            return 'Blocked';
        }

        return '';
    }

    protected function slotState($status, $activeBookings)
    {
        if ((int) $activeBookings > 0) {
            return 'booked';
        }

        $status = strtolower(trim((string) $status));

        return in_array($status, array('available', 'tersedia', 'aktif'), true) ? 'available' : 'unavailable';
    }

    protected function scheduleId($fieldId, $date, $start, $end)
    {
        return 'JWL' . strtoupper(substr(sha1($fieldId . '|' . $date . '|' . $start . '|' . $end), 0, 32));
    }

    protected function normalizeTime($time)
    {
        $time = trim((string) $time);

        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $time)) {
            return $time . ':00';
        }

        if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d:[0-5]\d$/', $time)) {
            return $time;
        }

        return '';
    }

    protected function timeToMinutes($time)
    {
        $time = trim((string) $time);

        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)/', $time, $matches)) {
            return null;
        }

        return ((int) $matches[1] * 60) + (int) $matches[2];
    }

    protected function minutesToTime($minutes)
    {
        $minutes = max(0, min(23 * 60 + 59, (int) $minutes));
        $hour = (int) floor($minutes / 60);
        $minute = $minutes % 60;

        return sprintf('%02d:%02d:00', $hour, $minute);
    }

    protected function parseDate($date)
    {
        if (!$this->isValidDate($date)) {
            return null;
        }

        try {
            return new \DateTimeImmutable($date);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    protected function isValidDate($date)
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) $date) === 1;
    }
}
