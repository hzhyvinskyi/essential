package shortener

type RedirectRepository interface {
	Find(string) (*Redirect, error)
	Store(redirect *Redirect) error
}
