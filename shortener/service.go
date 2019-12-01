package shortener

import (
	"errors"
	errs "github.com/pkg/errors"
	"github.com/teris-io/shortid"
	"gopkg.in/dealancer/validate.v2"
	"time"
)

var (
	ErrorRedirectNotFound = errors.New("Redirect Not Found")
	ErrorRedirectInvalid = errors.New("Redirect Invalid")
)

type RedirectService interface {
	Find(string) (*Redirect, error)
	Store(*Redirect) error
}

type redirectService struct {
	redirectRepository	RedirectRepository
}

func NewRedirectService(redirectRepository RedirectRepository) RedirectService {
	return &redirectService{redirectRepository}
}

func (r *redirectService) Find(code string) (*Redirect, error) {
	return r.redirectRepository.Find(code)
}

func (r *redirectService) Store(redirect *Redirect) error {
	if err := validate.Validate(redirect); err != nil {
		return errs.Wrap(ErrorRedirectInvalid, "service.Redirect.Store")
	}
	redirect.Code = shortid.MustGenerate()
	redirect.CreatedAt = time.Now().UTC().Unix()
	return r.redirectRepository.Store(redirect)
}
