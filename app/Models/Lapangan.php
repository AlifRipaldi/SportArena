<?php

namespace App\Models;

class Lapangan extends Model
{
    public function popular($limit = 4)
    {
        $limit = max(1, (int) $limit);
        $rows = array();
        $result = mysqli_query($this->db(),
            "SELECT l.*, COALESCE(AVG(r.Rating),0) AS Rating_avg, COUNT(r.ID_Review) AS Review_count,
                    (SELECT j.ID_Jadwal FROM jadwal j
                     WHERE j.ID_Lapangan=l.ID_Lapangan AND j.Tanggal>=CURDATE()
                       AND LOWER(j.Status) IN ('available','tersedia','aktif')
                     ORDER BY j.Tanggal,j.Jam_Mulai LIMIT 1) AS ID_Jadwal
             FROM lapangan l
             LEFT JOIN review r ON r.ID_Lapangan=l.ID_Lapangan AND LOWER(r.Status)='tampil'
             WHERE LOWER(l.Status)='aktif' AND l.deleted_at IS NULL
             GROUP BY l.ID_Lapangan
             ORDER BY Review_count DESC,l.created_at DESC LIMIT " . $limit
        );

        if (!$result) {
            return $rows;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        return $rows;
    }

    public function allByOwner($ownerId)
    {
        $rows = array();
        $statement = mysqli_prepare(
            $this->db(),
            'SELECT * FROM lapangan WHERE ID_Pemilik = ? AND deleted_at IS NULL ORDER BY created_at DESC, ID_Lapangan DESC'
        );

        if (!$statement) {
            return $rows;
        }

        mysqli_stmt_bind_param($statement, 's', $ownerId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);

        while ($result && $row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
        }

        mysqli_stmt_close($statement);

        return $rows;
    }

    public function findForOwner($id, $ownerId)
    {
        $statement = mysqli_prepare(
            $this->db(),
            'SELECT * FROM lapangan WHERE ID_Lapangan = ? AND ID_Pemilik = ? LIMIT 1'
        );

        if (!$statement) {
            return null;
        }

        mysqli_stmt_bind_param($statement, 'ss', $id, $ownerId);
        mysqli_stmt_execute($statement);

        $result = mysqli_stmt_get_result($statement);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($statement);

        return $row ?: null;
    }

    public function createForOwner($ownerId, array $data)
    {
        $id = $this->generateId();
        $facilities = $this->encodeFacilities(isset($data['facilities']) ? $data['facilities'] : array());
        $photos = $this->encodePhotos(isset($data['photos']) ? $data['photos'] : array());
        $statement = mysqli_prepare(
            $this->db(),
            'INSERT INTO lapangan (ID_Lapangan, Nama_lapangan, Lokasi, Jenis_olahraga, Fasilitas, ID_Pemilik, Harga, Status, Deskripsi, Foto) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$statement) {
            return false;
        }

        $name = isset($data['name']) ? $data['name'] : '';
        $location = isset($data['location']) ? $data['location'] : '';
        $type = isset($data['type']) ? $data['type'] : '';
        $price = isset($data['price']) ? (int) $data['price'] : 0;
        $status = isset($data['status']) ? $data['status'] : 'Aktif';
        $description = isset($data['description']) ? $data['description'] : '';

        mysqli_stmt_bind_param(
            $statement,
            'ssssssisss',
            $id,
            $name,
            $location,
            $type,
            $facilities,
            $ownerId,
            $price,
            $status,
            $description,
            $photos
        );

        $saved = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $saved;
    }

    public function updateForOwner($id, $ownerId, array $data)
    {
        $facilities = $this->encodeFacilities(isset($data['facilities']) ? $data['facilities'] : array());
        $photos = $this->encodePhotos(isset($data['photos']) ? $data['photos'] : array());
        $statement = mysqli_prepare(
            $this->db(),
            'UPDATE lapangan SET Nama_lapangan = ?, Lokasi = ?, Jenis_olahraga = ?, Fasilitas = ?, Harga = ?, Status = ?, Deskripsi = ?, Foto = ? WHERE ID_Lapangan = ? AND ID_Pemilik = ?'
        );

        if (!$statement) {
            return false;
        }

        $name = isset($data['name']) ? $data['name'] : '';
        $location = isset($data['location']) ? $data['location'] : '';
        $type = isset($data['type']) ? $data['type'] : '';
        $price = isset($data['price']) ? (int) $data['price'] : 0;
        $status = isset($data['status']) ? $data['status'] : 'Aktif';
        $description = isset($data['description']) ? $data['description'] : '';

        mysqli_stmt_bind_param(
            $statement,
            'ssssisssss',
            $name,
            $location,
            $type,
            $facilities,
            $price,
            $status,
            $description,
            $photos,
            $id,
            $ownerId
        );

        $saved = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $saved;
    }

    public function deleteForOwner($id, $ownerId)
    {
        $statement = mysqli_prepare(
            $this->db(),
            "UPDATE lapangan SET Status = 'Nonaktif', deleted_at = NOW() WHERE ID_Lapangan = ? AND ID_Pemilik = ?"
        );

        if (!$statement) {
            return false;
        }

        mysqli_stmt_bind_param($statement, 'ss', $id, $ownerId);
        $deleted = mysqli_stmt_execute($statement);
        mysqli_stmt_close($statement);

        return $deleted;
    }

    protected function encodeFacilities(array $facilities)
    {
        $cleanFacilities = array();

        foreach ($facilities as $facility) {
            $facility = trim((string) $facility);

            if ($facility !== '' && !in_array($facility, $cleanFacilities, true)) {
                $cleanFacilities[] = $facility;
            }
        }

        return json_encode($cleanFacilities);
    }

    protected function encodePhotos(array $photos)
    {
        $cleanPhotos = array();

        foreach ($photos as $photo) {
            $photo = trim((string) $photo);

            if ($photo !== '' && strpos($photo, '..') === false && !in_array($photo, $cleanPhotos, true)) {
                $cleanPhotos[] = $photo;
            }
        }

        return json_encode($cleanPhotos);
    }

    protected function generateId()
    {
        return 'LPG' . date('ymdHis') . random_int(10, 99);
    }
}
