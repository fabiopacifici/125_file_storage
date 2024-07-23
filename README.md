# Step by step Laravel/Vue Mail

Segue una scaletta per riepilogare i passaggi della mailable. vista in classe.

## Back-end - API

Creiamo un nuovo model Lead e relativa migration

```php
// app/Models/Lead.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'message'];
}
```

```php
// database/migrations/aaaa_mm_gg_hhmmss_create_leads_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.

*
     * @return void
     */
    public function up()
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->text('message');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('leads');
    }
};
```

Creiamo adesso un nuovo oggetto Mailable che ci permetterà di configurare la mail che sarà poi inviata. Troveremo la nostra classe all’interno di `app/Mail`.

```bash
php artisan make:mail NewContact
```

La configurazione di un oggetto Mailable si può fare sfruttando i tre metodi messi a disposizione: `envelope`, `content` e `attachment`.

Il metodo `envelope` restituisce un oggetto di tipo `Envelope` che definisce l’oggetto della mail e in alcuni casi anche il destinatario.

Il metodo `content` restituisce un oggetto di tipo `Content` che definisce il template di Blade utilizzato per generare il contenuto della mail.

Aggiungiamo all'oggetto mailable la variabile d'istanza `$lead` per poter utilizzare e stampare i dettagli del contatto nel corpo dell'email

```php
// app/Mail/NewContact.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewContact extends Mailable
{
    use Queueable, SerializesModels;
  
  // se definiamo la variabile d'istanza come public
  // si può acceedere direttamente dalla view
  // altrimenti, se è dichiarata protected o private, la possiamo passare come di consueto
  public $lead;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($_lead)
    {
        $this->lead = $_lead;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
  replyTo: $this->lead->address,
        subject: 'Nuovo contatto',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.new-contact-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
```

Nella view che viene utilizzata come corpo dell'email è possibile utilizzare la variabile `$lead` per accedere ai dettagli del contatto

```php
// resources/views/emails/new-contact.blade.php

// ...

<h1>Ciao admin!</h1>
<p>
Hai ricevuto un nuovo messaggio, ecco qui i dettagli:<br>
Nome: {{ $lead->name }}<br>
Email: {{ $lead->email }}<br>
Messaggio:<br>
{{ $lead->message }}
</p>

// ...
```

Creiamo un nuovo controller `Api\LeadController`

```bash
php artisan make:controller Api\LeadController
```

Creiamo un metodo `store` nel nostro controller per memorizzare i dati dei form ed inviare le email

```php
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\Lead;
use App\Mail\NewContact;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all();

        // validiamo i dati "a mano" per poter gestire la risposta
        $validator = Validator::make($data, [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                // la funzione errors() della classe Validator resituisce un array
                // in cui la chiave è il campo soggetto a validazione
                // e il valore è un array di messaggi di errore
                'errors' => $validator->errors()
            ]);
        }

        // salviamo a db i dati inseriti nel form di contatto
        $new_lead = new Lead();
        $new_lead->fill($data);
        $new_lead->save();

        // inviamo la mail all'admin del sito, passando il nuovo oggetto Lead
        Mail::to('info@boolpress.com')->send(new NewContact($new_lead));

        return response()->json([
            'success' => true,
        ]);
    }
}
```

Creiamo una rotta api per l'invio dei dati del form di contatto

```php
// routes/api.php

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{slug}', [PostController::class, 'show']);

// Aggiungiamo la rotta per il form di contatto
Route::post('/contacts', [LeadController::class, 'store']);
```

Facciamo la registrazione a Mailtrap e recuperare la configurazione corretta da inserire nel file `.env`

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=0000
MAIL_USERNAME=000000abcdef
MAIL_PASSWORD=000000abcdef
MAIL_ENCRYPTION=tls
```

modificare anche gli altri due parametri relativi all’indirizzo che apparirà nel campo ****from**** e al nome associato

```
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

Adesso testia tutto con una richiesta POST fatta tramite Postman. Se tutto va a buon fine possiamo passare allo sviluppo nel nostro front-end.

### Front-end - Vue

Creiamo un componente Vue per gestire il form di contatto.

```jsx
// src/components/ContactForm.vue

<script>
import axios from 'axios';
import { store } from '../store';
export default {
    name: "ContactForm",
    data() {
        return {
            store,
            name: '',
            email: '',
            message: '',
            loading: false,
            success: false,
            errors: {}
        }
    },
    methods: {
        sendForm() {
            this.loading = true;
            const data = {
                name: this.name,
                email: this.email,
                message: this.message
            };

            // pulisco l'array con i messaggi
            this.errors = {};

            // Importante - Stiamo comunicando con Laravel, quindi non è più obbligatorio inserire gli headers con il Content-Type
            // come abbiamo fatto invece quando comunicavamo direttamente con gli script PHP
            axios.post(`${this.store.apiBaseUrl}/api/contacts`, data).then((response) => {
                this.success = response.data.success;
                if (!this.success) {
                    this.errors = response.data.errors;
                } else {
                    // ripulisco i campi di input
                    this.name = '';
                    this.email = '';
                    this.message = '';
                }
                this.loading = false;
            });
        },
    }
}
</script>

<template>
    <section class="contact_me py-5">
        <div class="inner-wrapper">
            <div class="container-fluid text-center">
                <h2 class="text-uppercase">contact me</h2>
                <div v-if="success" class="alert alert-success text-start" role="alert">
                    Messaggio inviato con successo!
                </div>
                <div class="row">
                    <form @submit.prevent="sendForm()" class="col-12 text-start">
                        <div class="mb-3">
                            <input class="border-0 border-bottom form-control" :class="{ 'is-invalid': errors.name }"
                                type="text" name="name" id="name" placeholder="Name" v-model="name">
                            <p v-for="(error, index) in errors.name" :key="`message-error-${index}`"
                                class="invalid-feedback">
                                {{ error }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <input class="border-0 border-bottom form-control" :class="{ 'is-invalid': errors.email }"
                                type="text" name="email" id="email" placeholder="Email" v-model="email">
                            <p v-for="(error, index) in errors.email" :key="`message-error-${index}`"
                                class="invalid-feedback">
                                {{ error }}
                            </p>
                        </div>
                        <div class="mb-3">
                            <textarea class="border-0 border-bottom form-control"
                                :class="{ 'is-invalid': errors.message }" name="message" id="message" cols="30"
                                rows="10" placeholder="Message" v-model="message"></textarea>
                            <p v-for="(error, index) in errors.message" :key="`message-error-${index}`"
                                class="invalid-feedback">
                                {{ error }}
                            </p>
                        </div>
                        <button class="btn btn-lg btn-primary text-white" type="submit" :disabled="loading">{{ loading ?
                                'Sending...' : 'Send'
                        }}</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped lang="scss">

</style>
```

Utilizziamo quindi il componente all’interno delle pagine in cui vogliamo mostrare il form (es. Homepage, Contatti, etc…)

```jsx
// src/pages/ContactUs.vue

<script>
import ContactForm from '../components/ContactForm.vue';
export default {
    name: "ContactUs",
    components: {
        ContactForm
    }
}
</script>

<template>
    <section>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="mt-5 mb-3">
                        Contatti
                    </h1>
                    <ContactForm></ContactForm>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>

</style>
```
