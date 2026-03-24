import { useEffect, useMemo, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import { getBookFileForReading } from '@api/library'
import { ApiError } from '@api/http'
import styles from './RenderPage.module.css'
import { Header } from '@components/Header/Header'

type ReaderTheme = 'light' | 'dark'
type ReaderFileType = 'pdf' | 'txt' | 'fb2' | 'unknown'

type Fb2Block =
  | { type: 'title'; text: string; level: 1 | 2 | 3 }
  | { type: 'subtitle'; text: string }
  | { type: 'paragraph'; html: string }
  | { type: 'epigraph'; html: string }
  | { type: 'text-author'; text: string }
  | { type: 'poem'; html: string }
  | { type: 'empty' }
  | { type: 'image'; src: string; alt: string }

const FONT_SIZE_MIN = 12
const FONT_SIZE_MAX = 40
const FONT_SIZE_STEP = 1

const READER_THEME_KEY = 'reader-theme'
const READER_FONT_SIZE_KEY = 'reader-font-size'

const clampFontSize = (value: number) => {
  return Math.min(FONT_SIZE_MAX, Math.max(FONT_SIZE_MIN, Math.round(value)))
}

const getNodeTag = (element: Element) => {
  const tag = element.tagName.toLowerCase()
  const parts = tag.split(':')
  return parts[parts.length - 1]
}

const getDirectChildren = (element: Element, tagName?: string) => {
  return Array.from(element.children).filter((child) => {
    if (!tagName) {
      return true
    }

    return getNodeTag(child) === tagName
  })
}

const escapeHtml = (value: string) =>
  value
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')

const getInitialTheme = (): ReaderTheme => {
  const savedTheme = localStorage.getItem(READER_THEME_KEY)
  return savedTheme === 'dark' ? 'dark' : 'light'
}

const getInitialFontSize = () => {
  const savedFontSize = Number(localStorage.getItem(READER_FONT_SIZE_KEY))

  if (Number.isFinite(savedFontSize) && savedFontSize > 0) {
    return clampFontSize(savedFontSize)
  }

  return 18
}

const getFileExtension = (fileName: string) => {
  const cleanName = fileName.split('?')[0].toLowerCase()
  const parts = cleanName.split('.')
  return parts.length > 1 ? parts[parts.length - 1] : ''
}

const detectFileType = (fileName: string, contentType: string): ReaderFileType => {
  const extension = getFileExtension(fileName)
  const normalizedContentType = contentType.toLowerCase()

  if (extension === 'pdf' || normalizedContentType.includes('pdf')) {
    return 'pdf'
  }

  if (extension === 'txt' || normalizedContentType.includes('text/plain')) {
    return 'txt'
  }

  if (
    extension === 'fb2' ||
    normalizedContentType.includes('xml') ||
    normalizedContentType.includes('fictionbook')
  ) {
    return 'fb2'
  }

  return 'unknown'
}

const getBinaryMap = (xml: Document) => {
  const binaryMap = new Map<string, string>()

  Array.from(xml.getElementsByTagName('binary')).forEach((binary) => {
    const id = binary.getAttribute('id')
    const contentType = binary.getAttribute('content-type') || 'image/jpeg'
    const data = (binary.textContent || '').replace(/\s+/g, '')

    if (!id || !data) {
      return
    }

    binaryMap.set(id, `data:${contentType};base64,${data}`)
  })

  return binaryMap
}

const renderInlineNode = (node: Node): string => {
  if (node.nodeType === Node.TEXT_NODE) {
    return escapeHtml(node.textContent ?? '')
  }

  if (node.nodeType !== Node.ELEMENT_NODE) {
    return ''
  }

  const element = node as Element
  const tag = getNodeTag(element)
  const content = Array.from(element.childNodes).map(renderInlineNode).join('')

  switch (tag) {
    case 'emphasis':
      return `<em>${content}</em>`
    case 'strong':
      return `<strong>${content}</strong>`
    case 'sub':
      return `<sub>${content}</sub>`
    case 'sup':
      return `<sup>${content}</sup>`
    case 'strikethrough':
      return `<s>${content}</s>`
    case 'code':
      return `<code>${content}</code>`
    case 'a': {
      const href =
        element.getAttribute('l:href') ||
        element.getAttribute('xlink:href') ||
        element.getAttribute('href') ||
        '#'

      return `<a href="${escapeHtml(href)}" target="_blank" rel="noreferrer">${content}</a>`
    }
    default:
      return content
  }
}

const buildParagraphHtml = (element: Element) => {
  return Array.from(element.childNodes).map(renderInlineNode).join('').trim()
}

const buildPoemHtml = (element: Element) => {
  const blocks: string[] = []

  getDirectChildren(element).forEach((child) => {
    const childTag = getNodeTag(child)

    if (childTag === 'title') {
      const titleLines = getDirectChildren(child, 'p')
        .map((item) => buildParagraphHtml(item))
        .filter(Boolean)

      if (titleLines.length > 0) {
        blocks.push(titleLines.join('<br />'))
      }

      return
    }

    if (childTag === 'stanza') {
      const stanzaLines = getDirectChildren(child)
        .map((line) => {
          const lineTag = getNodeTag(line)

          if (lineTag === 'v') {
            return buildParagraphHtml(line)
          }

          if (lineTag === 'empty-line') {
            return '<br />'
          }

          return ''
        })
        .filter(Boolean)

      if (stanzaLines.length > 0) {
        blocks.push(stanzaLines.join('<br />'))
      }

      return
    }

    if (childTag === 'text-author') {
      const authorText = child.textContent?.trim()

      if (authorText) {
        blocks.push(`<em>${escapeHtml(authorText)}</em>`)
      }
    }
  })

  return blocks.join('<br /><br />')
}

const parseFb2 = async (blob: Blob): Promise<Fb2Block[]> => {
  const xmlText = await blob.text()
  const xml = new DOMParser().parseFromString(xmlText, 'application/xml')

  if (xml.querySelector('parsererror')) {
    throw new Error('Не удалось разобрать FB2-файл')
  }

  const binaryMap = getBinaryMap(xml)
  const bodies = Array.from(xml.getElementsByTagName('body')).filter(
    (body) => !body.getAttribute('name')
  )

  const result: Fb2Block[] = []

  const pushParagraph = (element: Element, type: 'paragraph' | 'epigraph') => {
    const html = buildParagraphHtml(element)

    if (!html.replace(/<[^>]+>/g, '').trim()) {
      return
    }

    result.push({ type, html })
  }

  const pushImage = (element: Element) => {
    const href =
      element.getAttribute('l:href') ||
      element.getAttribute('xlink:href') ||
      element.getAttribute('href') ||
      ''

    const id = href.replace('#', '')
    const src = binaryMap.get(id)

    if (!src) {
      return
    }

    result.push({
      type: 'image',
      src,
      alt: id || 'image',
    })
  }

  const walk = (element: Element, level: 1 | 2 | 3 = 1) => {
    const tag = getNodeTag(element)

    switch (tag) {
      case 'section': {
        const nextLevel = level < 3 ? ((level + 1) as 1 | 2 | 3) : 3

        getDirectChildren(element).forEach((child) => {
          if (getNodeTag(child) === 'title') {
            walk(child, level)
          } else {
            walk(child, nextLevel)
          }
        })

        return
      }

      case 'title': {
        const titleLines = getDirectChildren(element, 'p')
          .map((item) => item.textContent?.trim() ?? '')
          .filter(Boolean)

        titleLines.forEach((text) => {
          result.push({
            type: 'title',
            text,
            level,
          })
        })

        return
      }

      case 'subtitle': {
        const text = element.textContent?.trim()

        if (text) {
          result.push({
            type: 'subtitle',
            text,
          })
        }

        return
      }

      case 'p':
        pushParagraph(element, 'paragraph')
        return

      case 'empty-line':
        result.push({ type: 'empty' })
        return

      case 'image':
        pushImage(element)
        return

      case 'epigraph':
      case 'cite': {
        getDirectChildren(element).forEach((child) => {
          const childTag = getNodeTag(child)

          if (childTag === 'p') {
            pushParagraph(child, 'epigraph')
            return
          }

          if (childTag === 'poem') {
            const html = buildPoemHtml(child)

            if (html) {
              result.push({
                type: 'epigraph',
                html,
              })
            }

            return
          }

          if (childTag === 'text-author') {
            const text = child.textContent?.trim()

            if (text) {
              result.push({
                type: 'text-author',
                text,
              })
            }
          }
        })

        return
      }

      case 'poem': {
        const html = buildPoemHtml(element)

        if (html) {
          result.push({
            type: 'poem',
            html,
          })
        }

        return
      }

      default:
        getDirectChildren(element).forEach((child) => walk(child, level))
    }
  }

  bodies.forEach((body) => {
    getDirectChildren(body).forEach((child) => walk(child, 1))
  })

  return result
}

export const RenderPage = () => {
  const navigate = useNavigate()
  const { fileId } = useParams<{ fileId: string }>()

  const [isLoading, setIsLoading] = useState(true)
  const [error, setError] = useState('')
  const [fileName, setFileName] = useState('')
  const [fileType, setFileType] = useState<ReaderFileType>('unknown')
  const [pdfUrl, setPdfUrl] = useState('')
  const [txtContent, setTxtContent] = useState('')
  const [fb2Blocks, setFb2Blocks] = useState<Fb2Block[]>([])
  const [theme, setTheme] = useState<ReaderTheme>(getInitialTheme)
  const [fontSize, setFontSize] = useState<number>(getInitialFontSize)
  const [draftTheme, setDraftTheme] = useState<ReaderTheme>(getInitialTheme)
  const [draftFontSize, setDraftFontSize] = useState<number>(getInitialFontSize)
  const [isSettingsOpen, setIsSettingsOpen] = useState(false)

  const isTextSizeSupported = fileType === 'txt' || fileType === 'fb2'

  useEffect(() => {
    return () => {
      if (pdfUrl) {
        URL.revokeObjectURL(pdfUrl)
      }
    }
  }, [pdfUrl])

  useEffect(() => {
    const parsedFileId = Number(fileId)

    if (!fileId || Number.isNaN(parsedFileId)) {
      navigate('/library', { replace: true })
      return
    }

    const token = localStorage.getItem('token')

    if (!token) {
      navigate('/signin', { replace: true })
      return
    }

    const loadFile = async () => {
      try {
        setIsLoading(true)
        setError('')
        setTxtContent('')
        setFb2Blocks([])

        const { blob, fileName, contentType } = await getBookFileForReading(parsedFileId, token)
        const nextFileType = detectFileType(fileName, contentType)

        setFileName(fileName)
        setFileType(nextFileType)

        if (nextFileType === 'pdf') {
          const nextPdfUrl = URL.createObjectURL(blob)

          setPdfUrl((prev) => {
            if (prev) {
              URL.revokeObjectURL(prev)
            }

            return nextPdfUrl
          })

          return
        }

        setPdfUrl((prev) => {
          if (prev) {
            URL.revokeObjectURL(prev)
          }

          return ''
        })

        if (nextFileType === 'txt') {
          const text = await blob.text()
          setTxtContent(text)
          return
        }

        if (nextFileType === 'fb2') {
          const blocks = await parseFb2(blob)
          setFb2Blocks(blocks)
          return
        }

        throw new Error('Формат файла не поддерживается')
      } catch (err) {
        if (err instanceof ApiError && err.status === 401) {
          localStorage.removeItem('token')
          navigate('/signin', { replace: true })
          return
        }

        setError(err instanceof Error ? err.message : 'Не удалось открыть книгу')
      } finally {
        setIsLoading(false)
      }
    }

    void loadFile()
  }, [fileId, navigate])

  const pageClassName = useMemo(() => {
    return `${styles.page} ${theme === 'dark' ? styles.pageDark : ''}`
  }, [theme])

  const handleOpenSettings = () => {
    setDraftTheme(theme)
    setDraftFontSize(fontSize)
    setIsSettingsOpen((prev) => !prev)
  }

  const handleDraftFontSizeChange = (value: number) => {
    if (!Number.isFinite(value)) {
      return
    }

    setDraftFontSize(clampFontSize(value))
  }

  const handleSaveSettings = () => {
    setTheme(draftTheme)
    setFontSize(draftFontSize)
    localStorage.setItem(READER_THEME_KEY, draftTheme)
    localStorage.setItem(READER_FONT_SIZE_KEY, String(draftFontSize))
    setIsSettingsOpen(false)
  }

  const paragraphs = txtContent
    .split(/\n{2,}/)
    .map((item) => item.replace(/\n/g, ' ').trim())
    .filter(Boolean)

  return (
    <main className={pageClassName}>
      <Header
        leftVariant="back"
        centerVariant="logo"
        rightVariant="settings"
        onBackClick={() => navigate(-1)}
        onSettingsClick={handleOpenSettings}
      />

      <section className={styles.content}>
        {isSettingsOpen ? (
          <div className={styles.settingsPanel}>
            <div className={styles.settingsTitle}>Настройки отображения</div>

            {isTextSizeSupported ? (
                <div className={styles.settingsSection}>
                    <div className={styles.fontSizeHeader}>
                    <div className={styles.settingsLabel}>Размер текста</div>

                    <div className={styles.numberInputWrap}>
                        <input
                        className={styles.numberInput}
                        type="number"
                        min={FONT_SIZE_MIN}
                        max={FONT_SIZE_MAX}
                        step={FONT_SIZE_STEP}
                        value={draftFontSize}
                        onChange={(event) => {
                            if (Number.isNaN(event.target.valueAsNumber)) {
                            return
                            }

                            handleDraftFontSizeChange(event.target.valueAsNumber)
                        }}
                        />
                        <span className={styles.inputSuffix}>px</span>
                    </div>
                    </div>

                    <div className={styles.fontSizeControls}>
                    <input
                        className={styles.range}
                        type="range"
                        min={FONT_SIZE_MIN}
                        max={FONT_SIZE_MAX}
                        step={FONT_SIZE_STEP}
                        value={draftFontSize}
                        onChange={(event) => handleDraftFontSizeChange(Number(event.target.value))}
                    />
                    </div>
                </div>
            ) : null}

            <div className={styles.settingsSection}>
              <div className={styles.settingsLabel}>Тема</div>

              <div className={styles.themeButtons}>
                <button
                  type="button"
                  className={`${styles.themeButton} ${
                    draftTheme === 'light' ? styles.themeButtonActive : ''
                  }`}
                  onClick={() => setDraftTheme('light')}
                >
                  Светлая
                </button>

                <button
                  type="button"
                  className={`${styles.themeButton} ${
                    draftTheme === 'dark' ? styles.themeButtonActive : ''
                  }`}
                  onClick={() => setDraftTheme('dark')}
                >
                  Тёмная
                </button>
              </div>
            </div>

            <button type="button" className={styles.saveButton} onClick={handleSaveSettings}>
              Сохранить
            </button>
          </div>
        ) : null}

        {isLoading ? <p className={styles.state}>Загрузка...</p> : null}
        {error ? <p className={styles.error}>{error}</p> : null}

        {!isLoading && !error && fileType === 'pdf' ? (
          <div className={styles.pdfWrap}>
            <iframe title={fileName || 'PDF reader'} src={pdfUrl} className={styles.pdfFrame} />
          </div>
        ) : null}

        {!isLoading && !error && fileType === 'txt' ? (
          <div className={styles.readerSurface}>
            <article className={styles.textReader} style={{ fontSize: `${fontSize}px` }}>
              {paragraphs.map((paragraph, index) => (
                <p key={`${index}-${paragraph.slice(0, 20)}`} className={styles.paragraph}>
                  {paragraph}
                </p>
              ))}
            </article>
          </div>
        ) : null}

        {!isLoading && !error && fileType === 'fb2' ? (
          <div className={styles.readerSurface}>
            <article className={styles.textReader} style={{ fontSize: `${fontSize}px` }}>
              {fb2Blocks.map((block, index) => {
                if (block.type === 'title') {
                  if (block.level === 1) {
                    return (
                      <h1 key={index} className={`${styles.title} ${styles.titleLevel1}`}>
                        {block.text}
                      </h1>
                    )
                  }

                  if (block.level === 2) {
                    return (
                      <h2 key={index} className={`${styles.title} ${styles.titleLevel2}`}>
                        {block.text}
                      </h2>
                    )
                  }

                  return (
                    <h3 key={index} className={`${styles.title} ${styles.titleLevel3}`}>
                      {block.text}
                    </h3>
                  )
                }

                if (block.type === 'subtitle') {
                  return (
                    <h4 key={index} className={styles.subtitle}>
                      {block.text}
                    </h4>
                  )
                }

                if (block.type === 'epigraph') {
                  return (
                    <blockquote
                      key={index}
                      className={styles.epigraph}
                      dangerouslySetInnerHTML={{ __html: block.html }}
                    />
                  )
                }

                if (block.type === 'text-author') {
                  return (
                    <div key={index} className={styles.textAuthor}>
                      {block.text}
                    </div>
                  )
                }

                if (block.type === 'poem') {
                  return (
                    <div
                      key={index}
                      className={styles.poem}
                      dangerouslySetInnerHTML={{ __html: block.html }}
                    />
                  )
                }

                if (block.type === 'image') {
                  return <img key={index} src={block.src} alt={block.alt} className={styles.image} />
                }

                if (block.type === 'empty') {
                  return <div key={index} className={styles.emptyLine} />
                }

                return (
                  <p
                    key={index}
                    className={styles.paragraph}
                    dangerouslySetInnerHTML={{ __html: block.html }}
                  />
                )
              })}
            </article>
          </div>
        ) : null}
      </section>
    </main>
  )
}