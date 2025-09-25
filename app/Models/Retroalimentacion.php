    <?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Retroalimentacion extends Model
    {
        protected $table = 'retroalimentaciones'; // <-- Agrega esta línea

        protected $fillable = [
            'user_id', 'test_id', 'utilidad', 'precision', 'comentario', 'carrera_id'
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }

        public function test()
        {
            return $this->belongsTo(Test::class);
        }

        public function carrera()
        {
            return $this->belongsTo(Carrera::class);
        }
    }